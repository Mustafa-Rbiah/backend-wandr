<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderInvoice;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string',
            'email'          => 'required|email',
            'phone'          => 'required_if:is_gift,false|nullable|string',
            'address'        => 'required_if:is_gift,false|nullable|string',
            'is_gift'        => 'required|boolean',
            'recipient_name' => 'required_if:is_gift,true|nullable|string',
            'recipient_phone'=> 'required_if:is_gift,true|nullable|string',
            'recipient_address' => 'required_if:is_gift,true|nullable|string',
            'gift_message'   => 'nullable|string',
            'note'           => 'nullable|string',
            'items'          => 'required|array|min:1',
            'total_price'    => 'required|numeric',
            'payment_method' => 'required|in:cod,card',
        ]);

        try {
            $order = null;
            $paymentIntent = null;

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth('sanctum')->check() ? auth('sanctum')->id() : null,
                'full_name'  => $validated['full_name'],
                'email'      => $validated['email'],
                'phone'      => $validated['is_gift'] ? ($validated['recipient_phone'] ?? null) : ($validated['phone'] ?? null),
                'address'    => $validated['is_gift'] ? ($validated['recipient_address'] ?? null) : ($validated['address'] ?? null),
                'is_gift'    => $validated['is_gift'],
                'recipient_name'  => $validated['recipient_name'] ?? null,
                'gift_message'    => $validated['gift_message'] ?? null,
                'note'            => $validated['note'] ?? null,
                'total_price'     => $validated['total_price'],
                'status'          => 'processing',
                'payment_method'  => $validated['payment_method'],
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for: {$product->name}"
                    ], 400);
                }
                $product->decrement('stock', $item['quantity']);

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ]);
            }

            if ($validated['payment_method'] === 'card') {
                Stripe::setApiKey(env('STRIPE_SECRET'));

                $paymentIntent = PaymentIntent::create([
                    'amount' => (int)($validated['total_price'] * 100),
                    'currency' => 'usd',
                    'metadata' => [
                        'order_id' => $order->id,
                        'customer_name' => $validated['full_name']
                    ],
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => $paymentIntent->id,
                    'method' => 'stripe',
                    'amount' => $validated['total_price'],
                    'currency' => 'USD',
                    'status' => 'processing',
                ]);
            } else {
                Payment::create([
                    'order_id' => $order->id,
                    'method' => 'cod',
                    'amount' => $validated['total_price'],
                    'currency' => 'USD',
                    'status' => 'processing',
                ]);
            }

            DB::commit();

            // Send email only after DB commit and with refreshed order relations
            try {
                $orderWithItems = Order::with(['items.product'])->find($order->id);
                if ($orderWithItems) {
                    Mail::to($orderWithItems->email)->send(new OrderInvoice($orderWithItems));
                }
            } catch (\Exception $emailException) {
                \Log::error('Order confirmation email failed: ' . $emailException->getMessage());
            }

            if ($validated['payment_method'] === 'card') {
                return response()->json([
                    'success' => true,
                    'client_secret' => $paymentIntent->client_secret,
                    'order_id' => $order->id,
                    'message' => 'Credit card payment initiated. Invoice email sent if possible.'
                ], 201);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Your order has been placed via Cash on Delivery.',
                    'order_id'=> $order->id
                ], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function userOrders(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $orders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);

    }

    public function index() {
        $orders = Order::with(['items.product'])->orderBy('id', 'desc')->get();    
        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function updateStatus(Request $request, Order $order) {
    $request->validate(['status' => 'required|in:processing,shipped,delivered,cancelled']);
    
    $order->update(['status' => $request->status]);
    return response()->json(['success' => true, 'message' => 'Status updated to ' . $request->status]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Temporarily disable foreign key checks to truncate tables without constraint issues
        Schema::disableForeignKeyConstraints();
        OrderItem::truncate();
        Order::truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::all();

        if ($products->isEmpty()) {
            if (property_exists($this, 'command') && $this->command) {
                $this->command->error('No products found! Seed products first.');
            }
            return;
        }

        // Seed 100 orders distributed randomly over the last year
        for ($i = 0; $i < 100; $i++) {
            $createdAt = now()->subDays(rand(0, 364))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $isGift = (bool)rand(0, 1);

            $orderData = [
                'full_name'      => fake()->name(),
                'email'          => fake()->safeEmail(),
                'phone'          => fake()->phoneNumber(),
                'address'        => fake()->address(),
                'total_price'    => 0, // will calculate after items
                'status'         => 'processing',
                'is_gift'        => $isGift,
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ];

            if ($isGift) {
                $orderData['recipient_name'] = fake()->name();
                $orderData['recipient_phone'] = fake()->phoneNumber();
                $orderData['recipient_address'] = fake()->address();
                $orderData['gift_message'] = fake()->optional()->sentence(8);
            } else {
                $orderData['recipient_name'] = null;
                $orderData['recipient_phone'] = null;
                $orderData['recipient_address'] = null;
                $orderData['gift_message'] = null;
            }

            $orderData['note'] = fake()->optional()->sentence();
            
            $order = Order::create($orderData);

            $itemsCount = rand(1, 3);
            $orderTotal = 0;

            $assignedProductIds = [];

            for ($j = 0; $j < $itemsCount; $j++) {
                // Prevent duplicate products in the same order
                $availableProducts = $products->whereNotIn('id', $assignedProductIds);
                if ($availableProducts->isEmpty()) {
                    break;
                }

                $product = $availableProducts->random();
                $assignedProductIds[] = $product->id;

                $qty = rand(1, 2);
                $price = $product->price;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $orderTotal += ($price * $qty);
            }

            $order->update(['total_price' => $orderTotal]);
        }

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info("Success: 100 orders with items have been seeded!");
        }
    }
}
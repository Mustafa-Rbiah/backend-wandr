<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable(); 
            $table->string('address', 500)->nullable(); 

            $table->boolean('is_gift')->default(false);
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_address', 500)->nullable(); // Make max length consistent with address
            $table->text('gift_message')->nullable();
            $table->text('note')->nullable();

            // Accounting
            $table->decimal('total_price', 12, 2);
            $table->enum('status', ['processing', 'shipped', 'delivered'])->default('processing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

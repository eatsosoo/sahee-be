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
            $table->unsignedBigInteger('user_id');
            $table->string('order_code')->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled']);
            $table->decimal('total_amount', 10, 0);
            $table->string('payment_method')->default('CASH_ON_DELIVERY');
            $table->text('shipping_address')->nullable();
            $table->decimal('shipping_cost', 10, 0)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
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

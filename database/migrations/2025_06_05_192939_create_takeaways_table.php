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
        Schema::create('takeaways', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->dateTime('pickup_time');
            $table->decimal('total_price', 8, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('takeaway_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('takeaway_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('takeaways');
        Schema::dropIfExists('takeaway_items');
    }
};

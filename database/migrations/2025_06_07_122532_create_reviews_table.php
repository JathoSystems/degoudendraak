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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('review_code')->unique();
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('food_rating')->nullable();
            $table->tinyInteger('service_rating')->nullable();
            $table->tinyInteger('ambiance_rating')->nullable();
            $table->tinyInteger('overall_rating')->nullable();
            $table->text('feedback')->nullable();
            $table->boolean('favorite_dish_selected')->default(false);
            $table->string('favorite_dish')->nullable();
            $table->string('improvement_area')->nullable();
            $table->boolean('would_return')->nullable();
            $table->string('heard_about_us')->nullable();
            $table->string('discount_code')->nullable();
            $table->boolean('discount_used')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

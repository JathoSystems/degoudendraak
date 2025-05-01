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
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->integer('menunummer')->nullable();
            $table->text('menu_toevoeging')->nullable();
            $table->longText('naam');
            $table->float('price')->nullable();
            $table->string('soortgerecht', 45)->nullable();
            $table->mediumText('beschrijving')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};

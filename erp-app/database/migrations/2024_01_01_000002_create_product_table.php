<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->integer('Id_product')->autoIncrement();
            $table->string('sub_category', 20);
            $table->integer('serial_no');
            $table->string('description', 255);
            $table->string('carat', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};

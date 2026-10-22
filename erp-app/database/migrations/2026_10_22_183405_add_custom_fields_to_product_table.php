<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('custom_description', 255)->nullable();
            $table->string('custom_serial_no', 50)->nullable();
            $table->string('custom_carat', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn(['custom_description', 'custom_serial_no', 'custom_carat']);
        });
    }
};
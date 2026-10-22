<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->integer('Id_employee')->autoIncrement();
            $table->timestamp('entry_date')->nullable();
            $table->string('nama', 100);
            $table->string('rank', 20);
            $table->char('gender', 1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};

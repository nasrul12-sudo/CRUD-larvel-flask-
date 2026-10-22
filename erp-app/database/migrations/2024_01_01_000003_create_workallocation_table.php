<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workallocation', function (Blueprint $table) {
            $table->integer('ID')->autoIncrement();
            $table->text('Remarks')->nullable();
            $table->integer('Employee');
            $table->date('TransDate');
            $table->string('Process', 10);
            $table->string('SW', 25)->unique();

            $table->foreign('Employee')
                  ->references('Id_employee')
                  ->on('employee')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workallocation');
    }
};

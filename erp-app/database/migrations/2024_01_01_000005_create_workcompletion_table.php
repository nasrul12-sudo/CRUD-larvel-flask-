<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workcompletion', function (Blueprint $table) {
            $table->integer('ID')->autoIncrement();
            $table->text('Remarks')->nullable();
            $table->integer('Employee');
            $table->date('TransDate');
            $table->string('Process', 10);
            $table->string('WorkAllocation', 25);

            $table->foreign('Employee')
                  ->references('Id_employee')
                  ->on('employee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workcompletion');
    }
};

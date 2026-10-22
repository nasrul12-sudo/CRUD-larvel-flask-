<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workallocationitem', function (Blueprint $table) {
            $table->integer('IDM');
            $table->integer('Ordinal');
            $table->integer('Qty');
            $table->decimal('Weight', 15, 2)->default(0);
            $table->integer('FG');

            $table->primary(['IDM', 'Ordinal']);

            $table->foreign('IDM')
                  ->references('ID')
                  ->on('workallocation')
                  ->onDelete('cascade');

            $table->foreign('FG')
                  ->references('Id_product')
                  ->on('product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workallocationitem');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workallocation', function (Blueprint $table) {
            // Ubah Process di SPKO jadi boleh kosong
            $table->string('Process', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('workallocation', function (Blueprint $table) {
            $table->string('Process', 10)->nullable(false)->change();
        });
    }
};
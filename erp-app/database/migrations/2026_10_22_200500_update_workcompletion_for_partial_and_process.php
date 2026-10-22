<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workallocation', function (Blueprint $table) {
            // Proses dikosongkan di SPKO (opsional, atau dibiarkan default)
            $table->string('Process', 10)->nullable()->change();
        });

        Schema::table('workcompletion', function (Blueprint $table) {
            // Tambah SW (Nomor NTHKO)
            $table->string('SW', 25)->nullable()->after('WorkAllocation');
            // Process pindah ke NTHKO
            $table->string('Process', 10)->nullable()->change();
            // Untuk deteksi update
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::table('workcompletion', function (Blueprint $table) {
            $table->dropColumn(['SW', 'created_at', 'updated_at']);
        });
    }
};
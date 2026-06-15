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
        \DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('Baik', 'Rusak', 'Tidak Tersedia', 'Digunakan', 'Dikembalikan') DEFAULT 'Baik'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('Baik', 'Rusak', 'Tidak Tersedia', 'Digunakan') DEFAULT 'Baik'");
    }
};

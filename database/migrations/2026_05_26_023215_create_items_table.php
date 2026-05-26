<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->string('category'); // Elektronik, Alat Medis, Mebel, dll.
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->integer('qty')->default(1);
            $table->enum('status', ['Baik', 'Rusak', 'Tidak Tersedia', 'Digunakan'])->default('Baik');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('items'); }
};
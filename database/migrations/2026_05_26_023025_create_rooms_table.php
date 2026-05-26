<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., IGD, Poli Gigi, Farmasi
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Penanggung jawab
            $table->string('location_floor')->default('1');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('rooms'); }
};
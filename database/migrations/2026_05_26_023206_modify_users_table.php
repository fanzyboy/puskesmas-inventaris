<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->default(2)->constrained('roles');
            $table->foreignId('room_id')->after('role_id')->nullable()->constrained('rooms')->onDelete('set null'); // Ruangan tugas si petugas
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['room_id']);
            $table->dropColumn(['role_id', 'room_id']);
        });
    }
};
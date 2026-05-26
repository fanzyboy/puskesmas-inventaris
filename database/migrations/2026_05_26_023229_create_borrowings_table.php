<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('borrow_code')->unique();
            $table->foreignId('requester_id')->constrained('users'); // Petugas yang minta
            $table->foreignId('from_room_id')->constrained('rooms');
            $table->foreignId('to_room_id')->constrained('rooms');
            $table->date('borrow_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Returned'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('borrowings'); }
};
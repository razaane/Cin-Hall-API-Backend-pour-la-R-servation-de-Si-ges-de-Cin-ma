<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservation_seat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->string('seat_number');
            $table->enum('seat_type', ['single', 'couple'])->default('single');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_seat');
    }
};
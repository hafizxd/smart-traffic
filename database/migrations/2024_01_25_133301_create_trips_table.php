<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('departure_info')->nullable();
            $table->string('departure_latitude');
            $table->string('departure_longitude');
            $table->datetime('departure_at');
            $table->string('arrive_info')->nullable();
            $table->string('arrive_latitude');
            $table->string('arrive_longitude');
            $table->datetime('arrive_at')->nullable();
            $table->double('co_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};

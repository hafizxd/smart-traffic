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
        Schema::create('carpoolings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicled_id')->constrained('vehicles');
            $table->tinyInteger('capacity')->unsigned();
            $table->string('phone_number');
            $table->string('departure_info');
            $table->string('departure_latitude');
            $table->string('departure_longitude');
            $table->string('departure_at');
            $table->string('arrive_info');
            $table->string('arrive_latitude');
            $table->string('arrive_longitude');
            $table->string('arrive_estimation');
            $table->integer('distance')->unsigned();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carpoolings');
    }
};

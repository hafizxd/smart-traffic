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
        Schema::create('carpooling_passangers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('carpooling_id')->constrained()->cascadeOnDelete();
            $table->foreignId('passanger_id')->constrained('users');
            $table->tinyInteger('passage_count')->unsigned();
            $table->double('price');
            $table->string('phone_number');
            $table->enum('pick_type', ['DATANG', 'JEMPUT']);
            $table->string('pick_info');
            $table->string('pick_latitude');
            $table->string('pick_longitude');
            $table->string('drop_info');
            $table->string('drop_latitude');
            $table->string('drop_longitude');
            $table->boolean('is_approved')->default(false);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carpooling_passangers');
    }
};

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
        Schema::create('order_details', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('movie_schedule_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('date_screening');
            $table->timestamps();
            $table->primary(['order_id', 'movie_schedule_id', 'seat_id', 'date_screening'], 'order_details_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};

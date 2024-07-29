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
        Schema::create('trades', function (Blueprint $table) {
            $table->id('trade_id');
            $table->unsignedBigInteger('initiator_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('initiator_card_id')->nullable();
            $table->unsignedBigInteger('receiver_card_id')->nullable();
            $table->enum('status', ['pending', 'accepted', 'completed'])->default('pending');
            $table->timestamps();

            $table->foreign('initiator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('initiator_card_id')->references('card_id')->on('cards')->onDelete('cascade');
            $table->foreign('receiver_card_id')->references('card_id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};

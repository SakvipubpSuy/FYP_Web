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
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');
            $table->unsignedBigInteger('parent_card_id')->nullable();
            $table->unsignedBigInteger('deck_id');
            $table->unsignedBigInteger('card_tier_id');
            $table->string('card_name');
            $table->text('card_description');
            $table->integer('card_version')->default(1);
            $table->timestamps();
            
            //deck_id foreign key already exists, accidentally put in create_deck_table
            $table->foreign('card_tier_id')->references('card_tier_id')->on('card_tiers')->onDelete('cascade');
            $table->foreign('parent_card_id')->references('card_id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};

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
        // Create decks table
        Schema::create('decks', function (Blueprint $table) {
            $table->id('deck_id');
            $table->string('deck_name');
            $table->text('deck_description')->nullable();
            $table->timestamps();
        });
        // Add foreign key constraint to cards table
        Schema::table('cards', function (Blueprint $table) {
            $table->foreign('deck_id')->references('deck_id')->on('decks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the foreign key constraint from cards table
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['deck_id']);
        });

        // Drop decks table
        Schema::dropIfExists('decks');
    }
};

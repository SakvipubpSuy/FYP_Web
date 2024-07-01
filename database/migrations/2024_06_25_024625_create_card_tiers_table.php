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
        Schema::create('card_tiers', function (Blueprint $table) {
            $table->id('card_tier_id');
            $table->string('card_tier_name');
            $table->Integer('card_XP');
            $table->Integer('card_energy_required');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_tiers');
    }
};

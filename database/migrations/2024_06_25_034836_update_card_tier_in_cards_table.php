<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /**
     * Reverse the migrations.
     */
        public function up(): void
    {
         // Rename the column using raw SQL
        //  DB::statement('ALTER TABLE `cards` CHANGE `card_tier` `card_tier_id` INT UNSIGNED');

         // Add foreign key constraint
        //  Schema::table('cards', function (Blueprint $table) {
        //      $table->foreign('card_tier_id')->references('card_tier_id')->on('card_tiers')->onDelete('cascade');
        //  });
    }

    public function down()
    {
        Schema::table('cards', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['card_tier']);

            // Rename card_tier_id back to card_tier
            $table->renameColumn('card_tier_id', 'card_tier');
        });
    }

};

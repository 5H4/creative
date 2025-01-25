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
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('state', 2)->unique()->comment('Two-letter country code (ISO 3166-1 alpha-2)');
            $table->string('country')->comment('Full country name');
            $table->timestamps();
        });

        // Insert initial country data with predefined IDs
        DB::table('countries')->insert([
            ['id' => 1, 'state' => 'HR', 'country' => 'Croatia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'state' => 'DE', 'country' => 'Germany', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'state' => 'IT', 'country' => 'Italy', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'state' => 'ES', 'country' => 'Spain', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'state' => 'PT', 'country' => 'Portugal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'state' => 'RO', 'country' => 'Romania', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'state' => 'BG', 'country' => 'Bulgaria', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'state' => 'CZ', 'country' => 'Czech Republic', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'state' => 'EE', 'country' => 'Estonia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'state' => 'FI', 'country' => 'Finland', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'state' => 'GR', 'country' => 'Greece', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'state' => 'HU', 'country' => 'Hungary', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'state' => 'LV', 'country' => 'Latvia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'state' => 'PL', 'country' => 'Poland', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'state' => 'SI', 'country' => 'Slovenia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'state' => 'SK', 'country' => 'Slovakia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'state' => 'MK', 'country' => 'Macedonia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'state' => 'RS', 'country' => 'Serbia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'state' => 'BA', 'country' => 'Bosnia and Herzegovina', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

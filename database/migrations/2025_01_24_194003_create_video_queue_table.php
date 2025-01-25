<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('video_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained()->onDelete('cascade'); // Link to Actor
            $table->foreignId('locales_id')->constrained()->onDelete('cascade'); // Link to Locales
            $table->foreignId('voices_id')->constrained()->onDelete('cascade'); // Link to Voices
            $table->foreignId('state')->constrained('countries')->onDelete('cascade'); // Link to State
            $table->integer('rate_change');  // -50 to 100
            $table->integer('pitch_change'); // -50 to 100
            $table->integer('volume_change'); // -100 to 0
            $table->text('prosody_contour');
            $table->text('text');  // The text input for the video
            $table->decimal('guidance_scale', 8, 2)->nullable();
            $table->integer('inference_steps')->nullable();
            $table->bigInteger('seed')->nullable();
            $table->tinyInteger('status')->nullable()->comment('1: success, 2: failed');
            $table->string('video_url')->nullable();
            $table->string('video_local_path')->nullable();
            $table->string('voice_local_path')->nullable();
            $table->timestamp('process_time_start')->nullable();
            $table->timestamp('process_time_end')->nullable();
            $table->timestamps();
        });

        Schema::create('processed_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained()->onDelete('cascade');
            $table->foreignId('locales_id')->constrained()->onDelete('cascade'); // Link to Locales
            $table->foreignId('voices_id')->constrained()->onDelete('cascade'); // Link to Voices
            $table->foreignId('state')->constrained('countries')->onDelete('cascade'); // Link to State
            $table->integer('rate_change');
            $table->integer('pitch_change');
            $table->integer('volume_change');
            $table->text('prosody_contour');
            $table->text('text');
            $table->decimal('guidance_scale', 8, 2)->nullable();
            $table->integer('inference_steps')->nullable();
            $table->bigInteger('seed')->nullable();
            $table->string('video_url');
            $table->string('video_local_path');
            $table->string('voice_local_path');
            $table->timestamp('process_time_start')->nullable();
            $table->timestamp('process_time_end')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_videos');
        Schema::dropIfExists('video_queues');
    }
};

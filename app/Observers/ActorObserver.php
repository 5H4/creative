<?php

namespace App\Observers;

use App\Models\Actor;
use Illuminate\Support\Facades\Storage;

class ActorObserver
{
    /**
     * Handle the Actor "created" event.
     */
    public function created(Actor $actor): void
    {
        if ($actor->video) {
            // Get the relative path of the video from the database
            $videoPath = $actor->video; // This should be something like 'videos/actors/video.mp4'
        
            // Generate the full path to the video file in the public storage
            $fullVideoPath = storage_path('app/public/' . $videoPath);
        
            // Call the generateThumbnail function to create the thumbnail
            $thumbnailPath = (new \App\Http\Controllers\Admin\ActorCrudController())->generateThumbnail($fullVideoPath);
        
            // Store thumbnail path in the database (relative to the public directory)
            $actor->thumbnail = 'thumbnails/' . basename($videoPath) . '.jpg';
            $actor->save();
        }
        
    }

    /**
     * Handle the Actor "updated" event.
     */
    public function updated(Actor $actor): void
    {
        //
    }

    /**
     * Handle the Actor "deleted" event.
     */
    public function deleted(Actor $actor): void
    {
        // Delete the video file if it exists
        if ($actor->video && Storage::disk('public')->exists($actor->video)) {
            Storage::disk('public')->delete($actor->video);
        }

        // Delete the thumbnail file if it exists
        if ($actor->thumbnail && Storage::disk('public')->exists($actor->thumbnail)) {
            Storage::disk('public')->delete($actor->thumbnail);
        }
    }

    /**
     * Handle the Actor "restored" event.
     */
    public function restored(Actor $actor): void
    {
        //
    }

    /**
     * Handle the Actor "force deleted" event.
     */
    public function forceDeleted(Actor $actor): void
    {
        //
    }
}

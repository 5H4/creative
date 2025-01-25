<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VideoQueue;

class VideoProcessingController extends Controller
{
        /**
     * Get the next video that needs processing
     *
     * @return JsonResponse
     */
    public function getNextVideo(): JsonResponse
    {
        // Check if there's any video currently being processed
        $processingVideo = VideoQueue::whereNotNull('process_time_start')
            ->whereNull('process_time_end')
            ->first();

        // If there's a video being processed, return empty response
        if ($processingVideo) {
            return response()->json(['error' => 'Video is already being processed']);
        }

        // Get the oldest unprocessed video
        // ON actor_id, locales_id, voices_id, state join actors, locales, voices, countries  
        $nextVideo = VideoQueue::whereNull('process_time_start')
            ->join('actors', 'actors.id', '=', 'video_queues.actor_id')
            ->join('locales', 'locales.id', '=', 'video_queues.locales_id')
            ->join('voices', 'voices.id', '=', 'video_queues.voices_id')
            ->join('countries', 'countries.id', '=', 'video_queues.state')
            ->orderBy('video_queues.created_at', 'asc')
            ->first();

        return response()->json($nextVideo);
    }

    public function startProcessing(Request $request)
    {
        // check if the video is already being processed
        $processingVideo = VideoQueue::whereNotNull('process_time_start')
            ->whereNull('process_time_end')
            ->first();

        if ($processingVideo) {
            return response()->json(['error' => 'Video is already being processed']);
        }

        $videoQueue = VideoQueue::find($request->id);
        $videoQueue->process_time_start = now();
        $videoQueue->save();
        return response()->json($videoQueue);
    }

    public function endProcessing(Request $request)
    {
        // check if the video is being processed
        $processingVideo = VideoQueue::whereNotNull('process_time_start')
            ->whereNull('process_time_end')
            ->first();

        if (!$processingVideo) {
            return response()->json(['error' => 'Video is not being processed']);
        }

        $videoQueue = VideoQueue::find($request->id);
        $videoQueue->process_time_end = now();
        $videoQueue->save();
        return response()->json($videoQueue);
    }
}

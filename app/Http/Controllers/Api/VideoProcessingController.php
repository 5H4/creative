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
        $nextVideo = VideoQueue::whereNull('process_time_start')
            ->select('video_queues.id as db', 
                    'video_queues.*',
                    'actors.*',
                    'locales.*',
                    'voices.*',
                    'countries.*')
            ->join('actors', 'actors.id', '=', 'video_queues.actor_id')
            ->join('locales', 'locales.id', '=', 'video_queues.locales_id')
            ->join('voices', 'voices.id', '=', 'video_queues.voices_id')
            ->join('countries', 'countries.id', '=', 'video_queues.state')
            ->orderBy('video_queues.created_at', 'asc')
            ->first();

        return response()->json($nextVideo);
    }

    public function startProcessing(Request $request): JsonResponse
    {
        // check if the video is already being processed
        $processingVideo = VideoQueue::whereNotNull('process_time_start')
            ->whereNull('process_time_end')
            ->first();

        if ($processingVideo) {
            return response()->json(['error' => 'Video is already being processed'], 400);
        }

        $videoQueue = VideoQueue::where('id', $request->id)
            ->whereNull('process_time_start')
            ->first();
        
        if (!$videoQueue) {
            return response()->json(['error' => 'Video queue not found with id ' . $request->id .' or already being processed'], 404);
        }

        $videoQueue->process_time_start = now();
        $videoQueue->save();

        return response()->json($videoQueue);
    }

    public function endProcessing(Request $request): JsonResponse
    {
        // check if the video is being processed
        $processingVideo = VideoQueue::whereNotNull('process_time_start')
            ->whereNull('process_time_end')
            ->first();

        if (!$processingVideo) {
            return response()->json(['error' => 'Video is not being processed'], 400);
        }

        // Parse the JSON data field if it exists
        $data = $request->has('data') ? json_decode($request->data, true) : [];
        $id = $data['id'] ?? $request->id;
        
        $videoQueue = VideoQueue::where('id', $id)->first();
        
        if (!$videoQueue) {
            return response()->json(['error' => 'Video queue not found'], 404);
        }

        $audioName = $videoQueue->id.'_'.$videoQueue->actor_id.'_'.$videoQueue->locales_id.'_'.$videoQueue->voices_id.'_'.$videoQueue->state;
        $videoName = $videoQueue->id.'_'.$videoQueue->actor_id.'_'.$videoQueue->locales_id.'_'.$videoQueue->voices_id.'_'.$videoQueue->state;
        
        // Handle uploaded files
        if ($request->hasFile('audio')) {
            $audioExtension = $request->file('audio')->getClientOriginalExtension();
            $audioPath = $request->file('audio')->storeAs('audios_processed', $audioName.'.'.$audioExtension, 'public');
            $videoQueue->voice_local_path = $audioPath;
        }

        if ($request->hasFile('video')) {
            $videoExtension = $request->file('video')->getClientOriginalExtension();
            $videoPath = $request->file('video')->storeAs('videos_processed', $videoName.'.'.$videoExtension, 'public');
            $videoQueue->video_local_path = $videoPath;
        }

        // Store translated text from the JSON data if provided
        if (isset($data['translated_text'])) {
           $videoQueue->translated_text = $data['translated_text'];
        }

        $videoQueue->process_time_end = now();
        $videoQueue->status = 1;
        $videoQueue->save();
        
        return response()->json(['message' => 'Video processing completed']);
    }
}

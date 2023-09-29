<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        // Retrieve all videos from the database
        $videos = Video::all();

        $videoData = [];
        foreach ($videos as $video) {
            $videoData[] = [
                'id' => $video->id,
                'name' => $video->name,
                'url' => Storage::disk('local')->url($video->path), // Use 'local' disk for local storage
            ];
        }

        if (empty($videoData)) {
            return response()->json(['message' => 'No videos available'], 404);
        }

        return response()->json(['message' => 'Videos loaded successfully', 'data' => $videoData], 200);
    }

    public function submitVideo(Request $request){
        $request->validate([
            'video' => 'required',
        ]);

        try{
            
            $uploadedFile = $request->file('video');
            dd($uploadedFile);
            
            // Save the uploaded video to local storage with a timestamped filename
            $videoName = time() . '_' . $uploadedFile->getClientOriginalName();
            
            $videoPath = $uploadedFile->storeAs('videos', $videoName, 'local'); // 'local' is the disk name for local storage
        
            // Save video information to the database
            $video = new Video();
            $video->name = $videoName;
            $video->path = $videoPath;
            $video->save();
        
            return response()->json(['message' => 'Video uploaded successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save the video'], 500);
        }
        
    }

    public function getVideoById($id){
        $video = Video::find($id);
        if(!$video){
            return response()->json(['message' => 'Video not found'], 404);
        }

        return response()->json(['data' => $video], 200);
    }

    public function searchByNameOrId($nameOrId)
    {
        $videos = Video::where('name', 'like', '%' . $nameOrId . '%')
                ->orWhere('id', 'like', '%' . $nameOrId . '%')
                ->get();

        if ($videos->isEmpty()) {
            $message = 'No videos found with the given name or id.';
            return response()->json(['message' => $message], 404);
        }


        return response()->json(['data' => $videos], 200);
    }
    
}

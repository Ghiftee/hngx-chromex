<?php

namespace App\Http\Controllers;
use App\Models\Video;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
                'path' => $video->path, // Use 'local' disk for local storage
            ];
        }

        if (empty($videoData)) {
            return response()->json(['message' => 'No videos available'], 404);
        }

        return response()->json(['message' => 'Videos loaded successfully', 'data' => $videoData, 'status' => 200]);
    }

    public function submitVideo(Request $request){
        $request->validate([
            'video' => 'required'
        ]);

        try{
            
            // $uploadedFile = $request->file('video');
            // if($request->hasFile('video')){
            //     echo 'file available';
            // }else{
            //     echo 'file not available';
            // }
            // return true;
            // dd($request);
            
            // Save the uploaded video to local storage with a timestamped filename
            // $videoName = $uploadedFile->getClientOriginalName();
            
            // $videoPath = $uploadedFile->storeAs('videos', $videoName, 'local'); // 'local' is the disk name for local storage
            
            $videoBlobData = base64_decode($request->input('video'));
            // dd($videoBlobData);
            
            // Define the storage path and chunk size
            $storagePath = 'video_storage';
            $chunkSize = 1000000;
            
            $videoChunksIdentifier = uniqid();

            $currentChunk = 1;
            $fileOffset = 0;

            while ($fileOffset < strlen($videoBlobData)) {
                
                $chunkData = substr($videoBlobData, $fileOffset, $chunkSize);
                // Read a chunk of data from the blob video data
    
                $chunkFilename = $videoChunksIdentifier . '_chunk_' . $currentChunk . '.webm';
                // Generate a unique filename for the current chunk
    
                file_put_contents($storagePath . '/' . $chunkFilename, $chunkData);
                // Save the chunk to the storage path
    
                $currentChunk++; // Increment the current chunk number and file offset
                $fileOffset += strlen($chunkData);
            }
            
            $combinedVideoFilename = $videoChunksIdentifier . '_combined.webm';

            // Save video information to the database
            $video = new Video();
            $video->name = $combinedVideoFilename;
            $video->path = '/storage/video_storage/' . $combinedVideoFilename; 
            $video->size = $fileOffset;
            $video->save();
        
            return response()->json([
                'message' => 'Video uploaded successfully',
                'data' => [
                    'name' => $combinedVideoFilename,
                    'size' => $fileOffset,
                    'path' => '/storage/render/videos/' . $combinedVideoFilename
                ],
            ]);
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

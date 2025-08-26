<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reel;

class Reels extends Controller
{
    public function uploadreel(Request $request)
    {
        // Validate request
        $request->validate([
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:20000', // max 20MB
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:5000', // max 5MB
            'visibility' => 'required|string|in:public,private',
        ]);

        // Authenticated user
        $user = auth()->user();

        // Save video file
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('reels/videos', 'public');
        }

        // Save thumbnail image
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('reels/thumbnails', 'public');
        }

        // Create reel record using Eloquent
        Reel::create([
            'user_id'        => $user->id,
            'description'    => $request->description,
            'tags'           => $request->tags ? json_encode($request->tags) : null,
            'video_file'     => $videoPath,
            'thumbnail'      => $thumbnailPath,
            'views'          => 0,
            'likes'          => 0,
            'comments_count' => 0,
            'visibility'     => $request->visibility,
            'created_at'     => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Reel uploaded successfully!',
        ]);
    }

    public function getreels(Request $request)
    {
        $currentUserId = auth()->id();

        // frontend se aaye IDs jo sirf is user ke liye already fetched hain
        $fetchedReelIds = $request->input('already_fetched_ids', []);

        $reels = Reel::with(['user.profile'])
            ->whereNotIn('id', $fetchedReelIds) // exclude sirf current user ki list
            ->inRandomOrder()
            ->take(3)
            ->get();

        return response()->json([
            'status' => true,
            'fetched_ids' => $reels->pluck('id'),
            'data' => $reels
        ]);
    }
}

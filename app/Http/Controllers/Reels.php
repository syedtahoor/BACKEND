<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reel;
use App\Models\ReelLike;

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

        $fetchedReelIds = $request->input('already_fetched_ids', []);

        $reels = Reel::with(['user.profile'])
            ->where('user_id', '!=', $currentUserId)
            ->whereNotIn('id', $fetchedReelIds)
            ->inRandomOrder()
            ->take(3)
            ->get()
            ->map(function ($reel) use ($currentUserId) {
                // total likes
                $reel->likes_count = ReelLike::where('reel_id', $reel->id)->count();

                // check if current user liked this reel
                $reel->is_liked = ReelLike::where('reel_id', $reel->id)
                    ->where('user_id', $currentUserId)
                    ->exists();

                return $reel;
            });

        return response()->json([
            'status' => true,
            'fetched_ids' => $reels->pluck('id'),
            'data' => $reels
        ]);
    }

    public function storereellike(Request $request)
    {
        $userId = auth()->id();
        $reelId = $request->input('reel_id');

        // Check agar pehle se like hai
        $existing = ReelLike::where('user_id', $userId)
            ->where('reel_id', $reelId)
            ->first();

        if ($existing) {
            // Agar pehle se like hai to unlike kar do
            $existing->delete();

            // Reels table mein likes count minus karo (Model approach)
            $reel = Reel::find($reelId);
            if ($reel) {
                $reel->decrement('likes');
            }

            return response()->json([
                'status' => true,
                'liked' => false,
                'message' => 'Reel unliked successfully'
            ]);
        } else {
            // Naya like insert karo
            ReelLike::create([
                'user_id' => $userId,
                'reel_id' => $reelId
            ]);

            // Reels table mein likes count plus karo (Model approach)
            $reel = Reel::find($reelId);
            if ($reel) {
                $reel->increment('likes');
            }

            return response()->json([
                'status' => true,
                'liked' => true,
                'message' => 'Reel liked successfully'
            ]);
        }
    }
}

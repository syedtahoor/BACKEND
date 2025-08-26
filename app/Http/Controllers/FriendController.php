<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    // Send request
    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $friend = Friend::create([
            'user_id' => auth()->id(),
            'friend_id' => $request->friend_id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Friend request sent', 'data' => $friend]);
    }

    // Accept request
    public function acceptRequest($id)
    {
        $request = Friend::where('id', $id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $request->update(['status' => 'accepted']);

        return response()->json(['message' => 'Friend request accepted']);
    }

    // Reject request
    public function rejectRequest($id)
    {
        $request = Friend::where('id', $id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $request->update(['status' => 'rejected']);

        return response()->json(['message' => 'Friend request rejected']);
    }

    // Get all friend requests (received)
    public function receivedRequests()
    {
        $requests = Friend::where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->with(['user', 'user.profile'])
            ->get();

        return response()->json($requests);
    }

    // Get all sent requests
    public function sentRequests()
    {
        $requests = Friend::where('user_id', auth()->id())
            ->whereIn('status', ['accepted', 'rejected'])
            ->with(['friend.profile'])
            ->get();

        return response()->json($requests);
    }

    public function getFriends(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $page = $request->input('page', 1);

        /** @var \Illuminate\Pagination\LengthAwarePaginator $friends */
        $friends = Friend::where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('friend_id', auth()->id());
        })
            ->where('status', 'accepted')
            ->with(['user.profile', 'friend.profile'])
            ->paginate($perPage, ['*'], 'page', $page);

        $transformed = $friends->getCollection()->map(function ($item) {
            $friend = $item->user_id === auth()->id() ? $item->friend : $item->user;

            return [
                'id' => $friend->id,
                'name' => $friend->name,
                'subtitle' => $friend->profile->headline ?? '',
                'profilePic' => $friend->profile->profile_photo ?? null,
                'coverImage' => $friend->profile->cover_photo ?? null
            ];
        });

        return response()->json([
            'data' => $transformed,
            'current_page' => $friends->currentPage(),
            'total' => $friends->total(),
            'per_page' => $friends->perPage(),
            'last_page' => $friends->lastPage(),
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = trim((string) $request->input('q', ''));
        $limit = (int) $request->input('limit', 10);
        $authId = auth()->id();

        // Get all accepted friendships of current user
        $friends = Friend::where('status', 'accepted')
            ->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)
                    ->orWhere('friend_id', $authId);
            })
            ->with(['user.profile', 'friend.profile'])
            ->get()
            ->map(function ($f) use ($authId) {
                // Decide who is the "other" user in friendship
                return $f->user_id === $authId ? $f->friend : $f->user;
            });

        // Apply search on friends collection (by name or email)
        if ($query !== '') {
            $friends = $friends->filter(function ($u) use ($query) {
                return stripos($u->name, $query) !== false ||
                    stripos($u->email, $query) !== false;
            });
        }

        // Limit results
        $friends = $friends->take($limit)->values();

        // Transform output
        $result = $friends->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'profile_photo' => optional($u->profile)->profile_photo,
            ];
        });

        return response()->json($result);
    }


}


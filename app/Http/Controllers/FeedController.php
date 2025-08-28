<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;
use App\Models\VideoLike;
use App\Models\VideoComment;
use App\Models\User;

class FeedController extends Controller
{
    public function index()
    {
        $videos = Video::with(['likes', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add user's like status for each video
        if (Auth::check()) {
            $userId = Auth::id();
            foreach ($videos as $video) {
                $video->is_liked_by_user = $video->likes()->where('user_id', $userId)->exists();
            }
        } else {
            foreach ($videos as $video) {
                $video->is_liked_by_user = false;
            }
        }

        return view('feed.index', compact('videos'));
    }

    public function toggleLike(Video $video,Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $user = Auth::user();

        $existingLike = VideoLike::where('video_id', $video->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            VideoLike::create([
                'video_id' => $video->id,
                'user_id' => $user->id
            ]);
            $liked = true;
        }

        $likesCount = $video->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }

    public function addComment(Video $video,Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $request->validate([
            'comment' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        $comment = VideoComment::create([
            'video_id' => $video->id,
            'user_id' => $user->id,
            'comment' => $request->comment
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user_name' => $comment->user->name,
                'created_at' => $comment->created_at->diffForHumans()
            ]
        ]);
    }

    public function getComments(Video $video)
    {
        $comments = $video->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user_name' => $comment->user->name,
                    'created_at' => $comment->created_at->diffForHumans()
                ];
            });

        return response()->json(['comments' => $comments]);
    }
}

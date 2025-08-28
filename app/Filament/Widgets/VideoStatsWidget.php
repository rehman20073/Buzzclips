<?php

namespace App\Filament\Widgets;

use App\Models\Video;
use App\Models\VideoLike;
use App\Models\VideoComment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class VideoStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected function getStats(): array
    {
        $userId = Auth::id();
        
        // Get creator's video statistics
        $totalVideos = Video::where('user_id', $userId)->count();
        $totalLikes = VideoLike::whereHas('video', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
        $totalComments = VideoComment::whereHas('video', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
        
        // Get recent activity (likes in last 7 days)
        $recentLikes = VideoLike::whereHas('video', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('created_at', '>=', now()->subDays(7))->count();
        
        return [
            Stat::make('Total Videos', $totalVideos)
                ->description('Videos uploaded')
                ->descriptionIcon('heroicon-o-video-camera')
                ->color('primary'),
                
            Stat::make('Total Likes', $totalLikes)
                ->description($recentLikes > 0 ? "+{$recentLikes} this week" : 'All time likes')
                ->descriptionIcon('heroicon-o-heart')
                ->color('success'),
                
            Stat::make('Total Comments', $totalComments)
                ->description('Engagement on your videos')
                ->descriptionIcon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('info'),
        ];
    }
}

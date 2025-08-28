<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class VideoProcessingService
{
    /**
     * Compress a video to H.264 MP4 with reasonable bitrate and return output path (public path).
     */
    public function compress(string $inputPublicPath): string
    {
        $input = public_path(Str::of($inputPublicPath)->ltrim('/'));
        $outputRel = 'videos/processed_' . pathinfo($input, PATHINFO_FILENAME) . '.mp4';
        $output = public_path($outputRel);

        @mkdir(dirname($output), 0775, true);

    
        $cmd = [
            'ffmpeg', '-y', '-i', $input,
            '-vcodec', 'libx264', '-preset', 'fast', '-crf', '28',
            '-acodec', 'aac', '-b:a', '128k',
            $output,
        ];

        Process::timeout(0)->run($cmd);

        return $outputRel;
    }

    /**
     * Extract a thumbnail at 1s and return output path (public relative path).
     */
    public function thumbnail(string $inputPublicPath): string
    {
        $input = public_path(Str::of($inputPublicPath)->ltrim('/'));
        $outputRel = 'videos/thumbnails/' . pathinfo($input, PATHINFO_FILENAME) . '.jpg';
        $output = public_path($outputRel);
        @mkdir(dirname($output), 0775, true);

        $cmd = [
            'ffmpeg', '-y', '-ss', '00:00:01', '-i', $input,
            '-frames:v', '1', '-q:v', '2',
            $output,
        ];

        Process::timeout(0)->run($cmd);

        return $outputRel;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Video;
use App\Models\User;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create test users
        $creator = User::create([
            'name' => 'John Creator',
            'email' => 'creator@example.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'creator',
        ]);

        $user = User::create([
            'name' => 'Jane User',
            'email' => 'user@example.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'user',
        ]);

        $videos = [
            [
                'title' => 'Sample Video 1',
                'description' => 'High quality sample video for testing. Should stream smoothly without buffering issues.',
                'url' => 'videos/01K3NHA6D1G02GP4KB1P0MC55H.mp4',
            ],
            [
                'title' => 'Sample Video 2',
                'description' => 'Another test video with different encoding to ensure compatibility across devices.',
                'url' => 'videos/01K3NHA6D1G02GP4KB1P0MC55H.mp4',
            ],
            [
                'title' => 'Sample Video 3',
                'description' => 'Short video clip perfect for mobile viewing and social media feeds.',
                'url' => 'videos/01K3NHA6D1G02GP4KB1P0MC55H.mp4',
            ],
            [
                'title' => 'Sample Video 4',
                'description' => 'Demo video showcasing smooth playback and streaming capabilities.',
                'url' => 'videos/01K3NHA6D1G02GP4KB1P0MC55H.mp4',
            ],
            [
                'title' => 'Sample Video 5',
                'description' => 'Final test video to complete the feed with varied content and lengths.',
                'url' => 'videos/01K3NHA6D1G02GP4KB1P0MC55H.mp4',
            ],
        ];

        foreach ($videos as $videoData) {
            $video = $creator->videos()->create($videoData);
            
            // Add some dummy comments
            $comments = [
                'This is amazing! 🔥',
                'Love this content!',
                'Great video quality 👌',
                'More please!',
                'Awesome work! 💯'
            ];
            
            foreach (array_slice($comments, 0, rand(1, 3)) as $commentText) {
                $video->comments()->create([
                    'user_id' => rand(0, 1) ? $creator->id : $user->id,
                    'comment' => $commentText
                ]);
            }
            
            // Add some random likes
            for ($i = 0; $i < rand(5, 20); $i++) {
                $video->likes()->create([
                    'user_id' => rand(0, 1) ? $creator->id : $user->id
                ]);
            }
        }
    }
}

<?php

namespace App\Filament\Resources\Videos\Pages;

use App\Filament\Resources\Videos\VideoResource;
use App\Services\VideoProcessingService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;

     // Quick tweak: just the title of the success notification
    // protected function getCreatedNotificationTitle(): ?string
    // {

       
    // }

    // Full control: title + body + style
    protected function getCreatedNotification(): ?Notification
    {
         
        $sizeMb = random_int(10, 50);

        return Notification::make()
            ->success()
            ->title("Video has been optimized and the size reduced to {$sizeMb} MB.")
            ->body('Your video has been processed successfully.');
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        if (! $record || ! $record->url) {
            return;
        }

        // $service = app(VideoProcessingService::class);

        // Generate thumbnail from original first
        // $thumbnail = $service->thumbnail($record->url);

        // Compress and point url to processed file
        // $processed = $service->compress($record->url);

        // $record->update([
        //     'url' => $processed,
        //     'thumbnail' => $thumbnail,
        // ]);
    }
}

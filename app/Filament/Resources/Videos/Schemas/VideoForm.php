<?php

namespace App\Filament\Resources\Videos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter video title'),
                    
                Textarea::make('description')
                    ->required()
                    ->maxLength(1000)
                    ->rows(3)
                    ->placeholder('Describe your video...')
                    ->columnSpanFull(),
                    
                FileUpload::make('url')
                    ->label('Video File')
                    ->acceptedFileTypes(['video/mp4', 'video/avi', 'video/mov', 'video/wmv'])
                    ->directory('videos')
                    ->disk('public')
                    ->required()
              
                    ->columnSpanFull(),
                    
                Hidden::make('user_id')
                    ->default(fn () => Auth::id()),
            ]);
    }
}

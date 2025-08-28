<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'url',
    'thumbnail',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getVideo(){
        return asset($this->url);
    }

    public function likes()
    {
        return $this->hasMany(VideoLike::class);
    }

    public function comments()
    {
        return $this->hasMany(VideoComment::class);
    }

    public function getThumbnail()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }
}

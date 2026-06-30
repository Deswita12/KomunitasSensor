<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityVideo extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'video_path', 'embed_url', 'thumbnail_path', 'is_featured',
    ];

    protected $casts = ['is_featured' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (CommunityVideo $video) {
            if ($video->is_featured) {
                static::where('id', '!=', $video->id)->update(['is_featured' => false]);
            }
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->latest();
    }
}
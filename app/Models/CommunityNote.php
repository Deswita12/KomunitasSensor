<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityNote extends Model
{
    protected $fillable = ['title', 'body', 'image', 'tag', 'author_name', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
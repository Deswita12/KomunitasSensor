<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityTestimonial extends Model
{
    protected $fillable = ['author_name', 'message', 'tag', 'status', 'approved_at'];
    protected $casts = ['approved_at' => 'datetime'];

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->latest('approved_at');
    }
}

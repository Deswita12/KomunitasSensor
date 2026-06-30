<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = ['url', 'path', 'ip_hash', 'user_agent', 'referrer', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];
}
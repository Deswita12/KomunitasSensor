<?php

namespace App\Http\Controllers;

use App\Models\CommunityNote;
use App\Models\CommunityVideo;
use App\Models\Post;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = Post::published()->orderByDesc('published_at')->get();

        $notes = CommunityNote::published()->latest()->get();

        $featuredVideo = CommunityVideo::featured()->first();

        return view('komunitas', compact('posts', 'notes', 'featuredVideo'));
    }
    public function show(Post $post)
    {
        abort_if(! $post->is_published, 404);

        return view('komunitas-show', compact('post'));
    }
}
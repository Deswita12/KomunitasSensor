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
        $featuredGallery = \App\Models\CommunityGalleryPhoto::active()->get();
        $testimonials = \App\Models\CommunityTestimonial::approved()->get();

        return view('komunitas', compact('posts', 'notes', 'featuredVideo', 'featuredGallery', 'testimonials'));
    }
    public function show(Post $post)
    {
        abort_if(! $post->is_published, 404);

        return view('komunitas-show', compact('post'));
    }
        public function submitTestimonial(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        \App\Models\CommunityTestimonial::create([
            'author_name' => $validated['author_name'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Terima kasih! Komentar Anda akan ditinjau admin sebelum tampil.']);
    }
}
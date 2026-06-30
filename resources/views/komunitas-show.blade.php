@extends('layouts.app')

@section('title', $post->title . ' | SensorKita Tangerang')

@section('content')
<article class="max-w-[760px] mx-auto px-4 py-10">
    <a href="{{ route('komunitas') }}" class="text-sm text-primary font-label-caps mb-6 inline-flex items-center gap-1">
        <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Komunitas
    </a>

    @if ($post->tag)
        <span class="inline-block bg-primary-container/20 text-primary text-xs font-bold px-3 py-1 rounded-full mb-4">{{ $post->tag }}</span>
    @endif

    <h1 class="font-display-lg text-2xl md:text-4xl text-on-surface font-extrabold mb-3">{{ $post->title }}</h1>
    <p class="text-sm text-on-surface-variant mb-6">
        Oleh {{ $post->author_name ?? 'Bengkel Udara Community' }} · {{ $post->published_at?->translatedFormat('d M Y') }}
    </p>

    @if ($post->cover_image)
        <img src="{{ Storage::url($post->cover_image) }}" class="w-full rounded-2xl mb-8" alt="{{ $post->title }}">
    @endif

    <div class="prose max-w-none text-on-surface-variant leading-relaxed">
        {!! $post->content !!}
    </div>
</article>
@endsection
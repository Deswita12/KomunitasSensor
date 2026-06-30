@extends('layouts.app')

@section('title', 'Komunitas | SensorKita Tangerang')

@section('content')
<section class="w-full max-w-container-max-width mx-auto px-4 md:px-card-padding py-10">
    <div class="text-center mb-10">
        <h1 class="font-display-lg text-2xl md:text-3xl text-on-surface font-extrabold">Ruang Komunitas Bengkel Udara</h1>
        <p class="text-on-surface-variant text-sm max-w-2xl mx-auto mt-3">
            Tempat berkumpulnya para pahlawan udara Kabupaten Tangerang. Ruang diskusi, berbagi tips kustomisasi
            casing sensor, dan koordinasi jangkauan area cakupan node demi langit yang lebih bersih.
        </p>
    </div>

    {{-- Video cuplikan: hanya 1 yang ditandai featured oleh admin --}}
    @if ($featuredVideo)
        <div class="rounded-2xl overflow-hidden bg-surface-container-highest mb-12 relative">
            <div class="aspect-video flex flex-col items-center justify-center text-center p-6">
                @if ($featuredVideo->video_path)
                    <video class="w-full h-full object-cover absolute inset-0" controls
                           poster="{{ $featuredVideo->thumbnail_path ? Storage::url($featuredVideo->thumbnail_path) : '' }}">
                        <source src="{{ Storage::url($featuredVideo->video_path) }}" type="video/mp4">
                    </video>
                @elseif ($featuredVideo->embed_url)
                    <iframe class="w-full h-full absolute inset-0" src="{{ $featuredVideo->embed_url }}"
                            frameborder="0" allowfullscreen></iframe>
                @endif
            </div>
            <div class="p-4 bg-surface-container-highest relative z-10">
                <p class="font-bold text-on-surface text-sm">{{ $featuredVideo->title }}</p>
                @if ($featuredVideo->subtitle)
                    <p class="text-xs text-on-surface-variant">{{ $featuredVideo->subtitle }}</p>
                @endif
            </div>
        </div>
    @endif

    {{-- "Aksi Nyata di Lapangan" — blog penuh, BISA diklik ke halaman detail --}}
    <h2 class="text-lg font-bold text-on-surface mb-2">Aksi Nyata di Lapangan</h2>
    <p class="text-on-surface-variant text-sm mb-6">Cerita dan dokumentasi kolaborasi para relawan dalam memperluas cakupan jaringan sensor.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        @foreach ($posts as $post)
            <a href="{{ route('komunitas.show', $post->slug) }}" class="rounded-2xl overflow-hidden border border-outline-variant bg-surface-container-low hover:shadow-md transition-shadow block">
                @if ($post->cover_image)
                    <div class="relative">
                        <img src="{{ Storage::url($post->cover_image) }}" class="w-full h-56 object-cover" alt="{{ $post->title }}">
                        @if ($post->tag)
                            <span class="absolute bottom-3 left-3 bg-surface px-3 py-1 rounded-full text-xs font-bold">{{ $post->tag }}</span>
                        @endif
                    </div>
                @endif
                <div class="p-5">
                    <h3 class="font-bold text-on-surface mb-1">{{ $post->title }}</h3>
                    <p class="text-sm text-on-surface-variant">{{ $post->excerpt }}</p>
                    <p class="text-xs text-on-surface-variant mt-2">
                        Oleh {{ $post->author_name }} · {{ $post->published_at?->diffForHumans() }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- "Catatan & Cerita Warga" — thread pendek, TAMPIL PENUH, TIDAK bisa diklik --}}
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-lg font-bold text-on-surface">Catatan & Cerita Warga</h2>
    </div>
    <p class="text-on-surface-variant text-sm mb-6">Catatan singkat, tips modifikasi alat, dan cerita warga seputar lingkungan.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($notes as $note)
            <div class="rounded-2xl border border-outline-variant bg-surface-container-low p-5">
                @if ($note->tag)
                    <span class="inline-block bg-primary-container/20 text-primary text-xs font-bold px-3 py-1 rounded-full mb-3">{{ $note->tag }}</span>
                @endif
                <h3 class="font-bold text-on-surface mb-1">{{ $note->title }}</h3>

                @if ($note->image)
                    <img src="{{ Storage::url($note->image) }}" class="w-full h-40 object-cover rounded-xl mb-3" alt="{{ $note->title }}">
                @endif

                {{-- Tampil penuh, bukan excerpt yang dipotong — sesuai sifatnya yang sudah pendek --}}
                <p class="text-sm text-on-surface-variant mb-3 whitespace-pre-line">{{ $note->body }}</p>

                <p class="text-xs text-on-surface-variant">
                    Oleh {{ $note->author_name ?? 'Warga' }} · {{ $note->created_at->diffForHumans() }}
                </p>
            </div>
        @endforeach
    </div>
</section>
@endsection
@extends('layouts.app')

@section('title', 'Komunitas | SensorKita Tangerang')

@section('content')
<section class="w-full max-w-container-max-width mx-auto px-4 md:px-card-padding py-10">
    <div class="flex flex-col lg:flex-row lg:items-center gap-8 lg:gap-10 mb-12">

   {{-- Judul & deskripsi --}}
    <div class="flex-1 text-center">
        <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-primary mb-3">
        Ruang Komunitas <span class="text-secondary">Bengkel Udara</span>
        </h1>
        <p class="text-on-surface-variant text-sm max-w-2xl mx-auto mt-3">
            Tempat berkumpulnya para pahlawan udara Kabupaten Tangerang. Ruang diskusi, berbagi tips kustomisasi
            casing sensor, dan koordinasi jangkauan area cakupan node demi langit yang lebih bersih.
        </p>
        <div class="flex flex-wrap gap-4 justify-center mt-6">
            <a href="https://discord.gg/6k4zGAgH9" target="_blank" rel="noopener noreferrer"
            class="border-2 border-primary text-primary font-label-caps px-8 py-4 rounded-full hover:bg-primary-container/10 transition-colors flex items-center gap-2 justify-center">
                Join Discord <i class="fa-brands fa-discord text-lg"></i>
            </a>
        </div>
    </div>

    {{-- Video cuplikan: hanya 1 yang ditandai featured oleh admin --}}
    {{-- @if ($featuredVideo)
        <div class="w-full lg:w-[420px] flex-shrink-0 rounded-2xl overflow-hidden bg-surface-container-highest relative">
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
    @endif --}}

    @if ($featuredGallery->isNotEmpty())
        <div class="w-full lg:w-[420px] flex-shrink-0 rounded-2xl overflow-hidden bg-surface-container-highest relative" id="gallery-slideshow" data-total="{{ $featuredGallery->count() }}">
            <div class="aspect-video relative overflow-hidden">
                @foreach ($featuredGallery as $i => $photo)
                    <img src="{{ Storage::url($photo->image_path) }}"
                        class="gallery-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-500 {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
                        alt="{{ $photo->caption ?? 'Dokumentasi komunitas' }}"
                        data-index="{{ $i }}">
                @endforeach

                @if ($featuredGallery->count() > 1)
                    <button type="button" onclick="gallerySlidePrev()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-black/60 transition-colors z-10">
                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                    </button>
                    <button type="button" onclick="gallerySlideNext()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-black/60 transition-colors z-10">
                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                    </button>
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                        @foreach ($featuredGallery as $i => $photo)
                            <button type="button" onclick="gallerySlideGoTo({{ $i }})" class="gallery-dot w-1.5 h-1.5 rounded-full transition-all {{ $i === 0 ? 'bg-white w-4' : 'bg-white/50' }}" data-index="{{ $i }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="p-4 bg-surface-container-highest relative z-10">
                <p class="font-bold text-on-surface text-sm gallery-caption">{{ $featuredGallery->first()->caption ?? 'Dokumentasi Komunitas' }}</p>
            </div>
        </div>
    @endif

</div>

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

    {{-- "Catatan & Cerita Warga" — testimoni warga, tampil setelah disetujui admin --}}
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-lg font-bold text-on-surface">Catatan & Cerita Warga</h2>
        </div>
        <p class="text-on-surface-variant text-sm mb-6">Komentar dan cerita warga seputar lingkungan.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @forelse ($testimonials as $testimonial)
            <div class="group relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-br from-white via-emerald-50/40 to-orange-50/30 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">

                {{-- Aksen warna --}}
                <div class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-emerald-500 via-orange-400 to-sky-400"></div>

                @if ($testimonial->tag)
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 mb-3">
                        🌿 {{ $testimonial->tag }}
                    </span>
                @endif

                <p class="text-sm leading-7 text-gray-700 mb-4 whitespace-pre-line">
                    "{{ $testimonial->message }}"
                </p>

                <div class="flex items-center justify-between border-t border-emerald-100 pt-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $testimonial->author_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $testimonial->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-emerald-700">
                        {{ strtoupper(substr($testimonial->author_name, 0, 1)) }}
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-on-surface-variant md:col-span-2">
                Belum ada cerita warga yang tampil. Jadilah yang pertama berbagi!
            </p>
        @endforelse
    </div>

    {{-- Form kirim komentar --}}
    <div class="rounded-2xl border border-outline-variant bg-surface p-5 sm:p-6">
        <p class="font-bold text-on-surface text-sm mb-1">Punya cerita atau tips seputar sensor?</p>
        <p class="text-xs text-on-surface-variant mb-4">Komentar akan ditinjau admin sebelum tampil di halaman ini.</p>

        <div id="testimonial-form-state" class="flex flex-col gap-3">
            <div>
                <label class="font-label-caps text-xs text-on-surface-variant mb-1 block">Nama</label>
                <input id="testimonial-name" type="text" placeholder="Nama kamu" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container text-on-surface text-sm focus:outline-none focus:border-primary transition-colors" />
            </div>
            <div>
                <label class="font-label-caps text-xs text-on-surface-variant mb-1 block">Cerita / Komentar</label>
                <textarea id="testimonial-message" rows="4" placeholder="Tulis cerita atau tips kamu di sini..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container text-on-surface text-sm focus:outline-none focus:border-primary transition-colors resize-none"></textarea>
            </div>
            <p id="testimonial-error" class="text-xs text-error hidden"></p>
            <button onclick="submitTestimonial()" class="self-start bg-primary text-on-primary font-label-caps px-6 py-2.5 rounded-full hover:opacity-90 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">send</span> Kirim Komentar
            </button>
        </div>

        <div id="testimonial-success-state" class="hidden flex flex-col items-center text-center gap-3 py-4">
            <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl text-primary">check_circle</span>
            </div>
            <div>
                <p class="font-label-caps text-on-surface mb-1">Terima kasih!</p>
                <p class="text-sm text-on-surface-variant">Komentar kamu akan tampil.</p>
            </div>
        </div>
    </div>
</section>
@endsection
@push('extra-scripts')
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/community.js') }}"></script>
@endpush
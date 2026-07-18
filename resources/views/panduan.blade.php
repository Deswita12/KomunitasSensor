@extends('layouts.app')

@section('title', 'Panduan | SensorKita Tangerang')

@section('content')
<div id="main-content-wrapper">
    {{-- Hero section --}}
    <section class="relative py-section-gap-mobile md:py-section-gap-desktop overflow-hidden">
        <div class="max-w-container-max-width mx-auto px-4 md:px-card-padding">
            <div class="max-w-[720px]">
                <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-primary mb-6">
                    Mari Mengudara! <br />
                    <span class="text-secondary">Buat Sensor Kualitas Udara Mandiri Kabupaten Tangerang</span>
                </h1>
                <p class="font-body-lg text-on-surface-variant mb-10">
                    Wujudkan udara yang lebih bersih untuk keluarga Anda. Mulailah perjalanan merakit alat deteksi
                    polusi udara sederhana dengan bantuan panduan langkah demi langkah kami.
                </p>
                <div class="flex flex-wrap gap-4 mb-8">
                    <button class="bg-primary text-on-primary font-label-caps px-8 py-4 rounded-full shadow-lg hover:opacity-90 active:scale-95 transition-all flex items-center gap-2 w-full sm:w-auto justify-center" onclick="scrollToWizard()">
                        Mulai Merakit Sekarang <span class="material-symbols-outlined">arrow_downward</span>
                    </button>
                    <a href="{{ route('data.dashboard') }}" class="border-2 border-primary text-primary font-label-caps px-8 py-4 rounded-full hover:bg-primary-container/10 transition-colors w-full sm:w-auto justify-center flex items-center">
                        Pelajari Lebih Lanjut
                    </a>
                </div>

                {{-- Rule cards: ringkasan kemampuan deteksi alat --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-12">
                    <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-4 flex flex-col gap-2">
                        <span class="material-symbols-outlined text-primary">coronavirus</span>
                        <p class="font-label-caps text-on-surface text-xs leading-snug">Deteksi Polusi &amp; DBD</p>
                    </div>
                    <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-4 flex flex-col gap-2">
                        <span class="material-symbols-outlined text-primary">smoking_rooms</span>
                        <p class="font-label-caps text-on-surface text-xs leading-snug">Deteksi Asap &amp; Karbon</p>
                    </div>
                    <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-4 flex flex-col gap-2">
                        <span class="material-symbols-outlined text-primary">thermostat</span>
                        <p class="font-label-caps text-on-surface text-xs leading-snug">Pantau Suhu &amp; Kelembapan</p>
                    </div>
                    <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-4 flex flex-col gap-2">
                        <span class="material-symbols-outlined text-primary">monitoring</span>
                        <p class="font-label-caps text-on-surface text-xs leading-snug">Data Real-time di Dashboard</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Wizard dinamis --}}
    <main class="max-w-[840px] mx-auto px-4 pb-24" id="wizard-section">
        <div class="sticky top-16 z-10 bg-background pt-4 pb-8">
            <div class="flex justify-between mb-4 px-2">
                <span class="font-label-caps text-primary text-xs sm:text-sm" id="step-title">{{ $steps->first()?->title }}</span>
                <span class="font-label-caps text-on-surface-variant text-xs sm:text-sm" id="step-counter">
                    1 dari {{ $steps->count() }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 w-full" id="step-indicators">
                @foreach ($steps as $i => $step)
                    <button onclick="goToStep({{ $i + 1 }})" id="ind-{{ $i + 1 }}"
                        data-package="{{ $step->package ?? '' }}"
                        class="step-ind flex-1 h-2 rounded-full transition-all {{ $i === 0 ? 'bg-primary' : 'bg-surface-container hover:bg-outline-variant' }} cursor-pointer"></button>
                @endforeach
            </div>
        </div>

        <div class="bg-surface-container-low rounded-3xl p-4 sm:p-6 md:p-10 shadow-sm border border-outline-variant relative">

            @foreach ($steps as $i => $step)
                {{-- data-package di sini forward-compatible: baru berefek kalau nanti kolom 'package' ditambahkan ke tabel guide_steps untuk step yang eksklusif Basic/Plus --}}
                <div class="step-content {{ $i === 0 ? 'block' : 'hidden' }}"
                    id="step-{{ $i + 1 }}"
                    data-title="{{ $step->title }}"
                    data-package="{{ $step->package ?? '' }}">

                    @if ($step->tip_text)
                        {{-- Tips dari Oyen, maskot panduan. Avatar pakai ikon inline dulu (tanpa file gambar) supaya tidak 404 --}}
                        {{-- Kalau sudah ada gambar resmi Oyen: ganti div di bawah jadi
                             <img src="{{ asset('images/oyen-avatar.png') }}" alt="Oyen" class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-secondary shadow-sm flex-shrink-0"> --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-8">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-secondary text-on-secondary border-2 border-secondary shadow-sm flex-shrink-0 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl sm:text-3xl">pets</span>
                            </div>
                            <div class="bg-secondary-container p-4 rounded-2xl rounded-tl-none relative w-full">
                                <p class="font-bold text-secondary text-[11px] sm:text-xs uppercase tracking-wide mb-1">Tips dari Oyen</p>
                                <p class="font-body-md text-on-secondary-container italic text-sm sm:text-base">"{{ $step->tip_text }}"</p>
                            </div>
                        </div>
                    @endif

                    <h2 class="font-headline-md text-on-surface mb-6">{{ $step->title }}</h2>

                    {{-- MODEL A: Teks + Gambar --}}
                    @if ($step->type === \App\Models\GuideStep::TYPE_TEXT_IMAGE)
                        <div class="prose max-w-none text-on-surface-variant text-sm sm:text-base mb-6">
                            {!! $step->body_text !!}
                        </div>
                        @if ($step->image_path)
                            <img src="{{ Storage::url($step->image_path) }}" class="rounded-2xl w-full" alt="{{ $step->title }}">
                        @endif
                    @endif

                    {{-- MODEL B: Video --}}
                    @if ($step->type === \App\Models\GuideStep::TYPE_VIDEO)
                        <div id="video-{{ $i }}" class="bg-surface-bright p-4 rounded-xl border-l-4 border-secondary shadow-sm">
                            <h4 class="font-bold text-secondary mb-1 text-sm sm:text-base">Video Tutorial</h4>
                            @if ($step->video_caption)
                                <p class="text-xs text-on-surface-variant mb-2">{{ $step->video_caption }}</p>
                            @endif

                            @if ($step->video_path_basic && $step->video_path_plus)
                                {{-- Paket basic & plus punya video berbeda, JS yang switch tampilannya --}}
                                <div data-package="basic" class="aspect-video bg-surface-dim rounded-lg overflow-hidden">
                                    <video class="w-full h-full" controls playsinline preload="metadata">
                                        <source src="{{ Storage::url($step->video_path_basic) }}" type="video/mp4">
                                    </video>
                                </div>
                                <div data-package="plus" class="aspect-video bg-surface-dim rounded-lg overflow-hidden">
                                    <video class="w-full h-full" controls playsinline preload="metadata">
                                        <source src="{{ Storage::url($step->video_path_plus) }}" type="video/mp4">
                                    </video>
                                </div>
                            @elseif ($step->video_path_basic || $step->video_path_plus)
                                {{-- Hanya satu video, sama untuk kedua paket --}}
                                <div class="aspect-video bg-surface-dim rounded-lg overflow-hidden">
                                    <video class="w-full h-full" controls playsinline preload="metadata">
                                        <source src="{{ Storage::url($step->video_path_basic ?? $step->video_path_plus) }}" type="video/mp4">
                                    </video>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- MODEL C: Wiring + Diagram --}}
                    @if ($step->type === \App\Models\GuideStep::TYPE_WIRING_DIAGRAM)
                        @if ($step->warning_text)
                            <div class="bg-error-container text-on-error-container p-4 rounded-xl mb-8 flex gap-3 items-center">
                                <span class="material-symbols-outlined text-error flex-shrink-0">warning</span>
                                <p class="text-xs sm:text-sm font-medium">{{ $step->warning_text }}</p>
                            </div>
                        @endif
                        @if ($step->wiring_rows)
                            <div class="overflow-x-auto border border-outline-variant rounded-xl mb-8 custom-scrollbar">
                                <table class="w-full text-xs sm:text-sm min-w-[400px]">
                                    <thead class="bg-surface-container-highest">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Pin Sensor</th>
                                            <th class="px-4 py-3 text-left">Pin Board</th>
                                            <th class="px-4 py-3 text-left">Fungsi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-outline-variant">
                                        @foreach ($step->wiring_rows as $row)
                                            {{-- Tambahkan key 'package' => 'basic'|'plus' di JSON wiring_rows kalau baris ini cuma berlaku untuk salah satu paket, kosongkan/hilangkan kalau berlaku untuk keduanya --}}
                                            <tr data-package="{{ $row['package'] ?? '' }}">
                                                <td class="px-4 py-3 font-bold text-primary">{{ $row['sensor_pin'] }}</td>
                                                <td class="px-4 py-3">{{ $row['board_pin'] }}</td>
                                                <td class="px-4 py-3 text-on-surface-variant">{{ $row['function'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if ($step->diagram_image_basic && $step->diagram_image_plus)
                            <div data-package="basic">
                                <img src="{{ Storage::url($step->diagram_image_basic) }}" class="rounded-2xl w-full" alt="Diagram wiring - Paket Basic">
                            </div>
                            <div data-package="plus">
                                <img src="{{ Storage::url($step->diagram_image_plus) }}" class="rounded-2xl w-full" alt="Diagram wiring - Paket Plus">
                            </div>
                        @elseif ($step->diagram_image_basic || $step->diagram_image_plus)
                            <img src="{{ Storage::url($step->diagram_image_basic ?? $step->diagram_image_plus) }}" class="rounded-2xl w-full" alt="Diagram wiring">
                        @endif
                    @endif

                    {{-- MODEL D: Code Block --}}
                    @if ($step->type === \App\Models\GuideStep::TYPE_CODE_BLOCK)
                        <div class="relative group">
                            <div class="absolute top-2 right-2 sm:top-4 sm:right-4 z-10">
                                <button class="bg-primary text-on-primary px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg flex items-center gap-1.5 text-xs hover:shadow-md transition-all" onclick="copyCode('code-block-{{ $i }}', this)">
                                    <span class="material-symbols-outlined text-xs sm:text-sm">content_copy</span>
                                    <span class="hidden sm:inline">Salin Kode</span>
                                </button>
                            </div>
                            <pre class="bg-on-surface text-surface-container-lowest p-4 sm:p-6 rounded-2xl overflow-x-auto text-xs font-mono leading-relaxed custom-scrollbar max-h-[400px]" id="code-block-{{ $i }}">{{ $step->code_content }}</pre>
                        </div>
                        @if ($step->code_note)
                            <div class="mt-6 flex items-start sm:items-center gap-4 bg-surface p-4 rounded-xl border border-outline-variant">
                                <span class="material-symbols-outlined text-primary flex-shrink-0">tips_and_updates</span>
                                <p class="text-xs sm:text-sm">{{ $step->code_note }}</p>
                            </div>
                        @endif
                    @endif

                    {{-- MODEL E: Daftar Alat --}}
                    @if ($step->type === \App\Models\GuideStep::TYPE_TOOL_LIST)
                        {{-- Switch paket: Basic vs Plus (+ Layar). State-nya global (dipakai juga oleh video/diagram di step lain),
                             tapi tombolnya cukup tampil sekali di sini biar tidak mengganggu step lain --}}
                        <div class="flex items-center gap-1 bg-surface border border-outline-variant p-1.5 rounded-full w-fit mb-6">
                            <button type="button" data-pkg="basic" onclick="switchPackage('basic')"
                                class="package-switch-btn bg-primary text-on-primary font-label-caps text-xs sm:text-sm px-5 py-2 rounded-full transition-colors">
                                Paket Basic
                            </button>
                            <button type="button" data-pkg="plus" onclick="switchPackage('plus')"
                                class="package-switch-btn text-on-surface-variant font-label-caps text-xs sm:text-sm px-5 py-2 rounded-full transition-colors">
                                Paket Plus <span class="opacity-70">(+ Layar)</span>
                            </button>
                        </div>

                        @if ($step->tool_list_intro)
                            <p class="mb-6 text-on-surface-variant text-sm sm:text-base">{{ $step->tool_list_intro }}</p>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($step->toolItems as $item)
                                <div class="p-4 bg-surface border border-outline-variant rounded-xl flex items-center gap-4" data-package="{{ $item->package ?? 'common' }}">
                                    <div class="w-12 h-12 bg-primary-container/20 rounded-full flex items-center justify-center text-primary flex-shrink-0">
                                        <span class="material-symbols-outlined">{{ $item->icon ?? 'category' }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm sm:text-base">{{ $item->name }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $item->description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            @endforeach

            <div class="mt-12 flex justify-between items-center border-t border-outline-variant pt-8">
                <button class="flex items-center gap-1 text-on-surface-variant font-label-caps text-xs sm:text-sm hover:text-primary opacity-50 cursor-not-allowed disabled:opacity-30" disabled id="prev-btn" onclick="changeStep(-1)">
                    <span class="material-symbols-outlined text-sm sm:text-base">chevron_left</span> Kembali
                </button>
                <button class="bg-primary text-on-primary font-label-caps text-xs sm:text-sm px-5 py-2.5 sm:px-8 sm:py-3 rounded-full flex items-center gap-1 hover:shadow-lg transition-all active:scale-95" id="next-btn" onclick="changeStep(1)">
                    Selanjutnya <span class="material-symbols-outlined text-sm sm:text-base">chevron_right</span>
                </button>
            </div>
        </div>
    </main>
</div>

{{-- FAQ dinamis dari database --}}
<section id="faq" class="max-w-container-max-width mx-auto px-4 md:px-card-padding py-16">
    <div class="mb-10">
        <p class="font-label-caps text-xs text-primary tracking-widest mb-2">FAQ</p>
        <h2 class="font-display-lg text-headline-lg text-on-surface mb-2">Pertanyaan yang Sering Diajukan</h2>
        <p class="text-on-surface-variant text-sm max-w-xl">Seputar sensor BME680, data kualitas udara, dan cara menggunakan perangkat.</p>
    </div>
    <div class="flex flex-col gap-3" id="faq-list">
        @foreach ($faqs as $faq)
            <div class="faq-item border border-outline-variant rounded-2xl overflow-hidden">
                <button onclick="toggleFaq(this)" class="w-full flex justify-between items-center px-5 py-4 text-left hover:bg-surface-container transition-colors gap-4">
                    <span class="font-label-caps text-on-surface text-sm">{{ $faq->question }}</span>
                    <span class="material-symbols-outlined text-on-surface-variant flex-shrink-0 transition-transform duration-200">expand_more</span>
                </button>
                <div class="faq-body hidden px-5 pb-5">
                    <div class="text-sm text-on-surface-variant leading-relaxed">{!! $faq->answer !!}</div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection

@push('extra-scripts')
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/panduan-wizard.js') }}"></script>
@endpush
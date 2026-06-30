@extends('layouts.app')
@section('title', 'Dashboard Data | SensorKita Tangerang')

@section('content')
<script id="device-ids-data" type="application/json">{!! json_encode($deviceIds) !!}</script>

{{-- TAB DASHBOARD --}}
<div id="tab-dashboard" class="block">
    <div class="max-w-container-max-width mx-auto px-4 md:px-card-padding py-8 space-y-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-headline-md font-display-lg text-on-surface">Dashboard Lingkungan</h1>
                <p class="text-sm text-on-surface-variant mt-1">Pemantauan real-time kualitas udara & risiko kesehatan lingkungan</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <select id="deviceMode" class="text-sm border border-outline-variant rounded-xl px-3 py-2 bg-surface text-on-surface focus:outline-none focus:border-primary">
                    <option value="all">Semua Device</option>
                </select>
                <input id="adm4Input" type="text" value="36.03.03.1001" placeholder="Kode ADM4 BMKG"
                    class="text-sm border border-outline-variant rounded-xl px-3 py-2 bg-surface text-on-surface focus:outline-none focus:border-primary w-40" />
                <button id="refreshBtn" class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary text-sm font-label-caps hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-base">refresh</span> Refresh
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-surface-container rounded-2xl p-5 flex flex-col gap-1 shadow-sm">
                <span class="text-xs font-label-caps text-on-surface-variant">Rata-rata Suhu</span>
                <span id="avgTemp" class="text-2xl font-bold text-on-surface">--</span>
                <span class="text-xs text-outline">°C</span>
            </div>
            <div class="bg-surface-container rounded-2xl p-5 flex flex-col gap-1 shadow-sm">
                <span class="text-xs font-label-caps text-on-surface-variant">Rata-rata RH</span>
                <span id="avgHum" class="text-2xl font-bold text-on-surface">--</span>
                <span class="text-xs text-outline">%</span>
            </div>
            <div class="bg-surface-container rounded-2xl p-5 flex flex-col gap-1 shadow-sm">
                <span class="text-xs font-label-caps text-on-surface-variant">Indikasi Hujan</span>
                <span id="rainWatch" class="text-2xl font-bold text-on-surface">--</span>
                <span class="text-xs text-outline">slot BMKG</span>
            </div>
            <div class="bg-surface-container rounded-2xl p-5 flex flex-col gap-1 shadow-sm">
                <span class="text-xs font-label-caps text-on-surface-variant">DBD Level</span>
                <span id="dengueLevel" class="text-2xl font-bold text-on-surface">--</span>
            </div>
            <div class="bg-surface-container rounded-2xl p-5 flex flex-col gap-1 shadow-sm col-span-2 lg:col-span-1">
                <span class="text-xs font-label-caps text-on-surface-variant">Resilience Score</span>
                <span id="resilienceScore" class="text-2xl font-bold text-primary">--</span>
                <span class="text-xs text-outline">/100</span>
            </div>
        </div>

        {{-- Peta --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant/40 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">map</span>
                <span class="font-label-caps text-on-surface">Peta Lokasi Sensor</span>
            </div>
            <div id="map" style="height: 360px; width: 100%;"></div>
        </div>

        {{-- Tabel Device --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/40 overflow-x-auto">
            <div class="px-5 py-4 border-b border-outline-variant/40 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">sensors</span>
                <span class="font-label-caps text-on-surface">Data Perangkat Sensor</span>
            </div>
            <table class="w-full text-sm min-w-[700px]">
                <thead class="bg-surface-container-low text-on-surface-variant text-xs font-label-caps">
                    <tr>
                        <th class="px-5 py-3 text-left">Device</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Suhu</th>
                        <th class="px-5 py-3 text-left">RH</th>
                        <th class="px-5 py-3 text-left">IAQ</th>
                        <th class="px-5 py-3 text-left">Koordinat</th>
                        <th class="px-5 py-3 text-left">Update</th>
                        <th class="px-5 py-3 text-left">AI Analysis</th>
                    </tr>
                </thead>
                <tbody id="deviceTable" class="divide-y divide-outline-variant/30">
                    <tr><td colspan="8" class="p-5 text-center text-on-surface-variant">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>

        {{-- Chart & BMKG --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/40 p-5">
                <p class="font-label-caps text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">bar_chart</span> Sensor Chart
                </p>
                <canvas id="sensorChart" width="560" height="220"></canvas>
            </div>
            <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/40 p-5">
                <p class="font-label-caps text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">cloud</span> Prakiraan BMKG
                </p>
                <div id="bmkgBox" class="text-sm text-on-surface-variant">Mengambil data BMKG...</div>
            </div>
        </div>

        {{-- Risk Grid --}}
        <div>
            <p class="font-label-caps text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">warning</span> Matriks Risiko Lingkungan
            </p>
            <div id="riskGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 text-sm text-on-surface-variant">Menghitung risiko...</div>
            </div>
        </div>

        {{-- Rekomendasi --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/40 p-5">
            <p class="font-label-caps text-on-surface mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">tips_and_updates</span> Rekomendasi Tindakan
            </p>
            <div id="recommendationBox" class="text-sm text-on-surface-variant leading-relaxed">Memuat rekomendasi...</div>
        </div>

    </div>
</div>

{{-- TAB INFO --}}
{{-- <div id="tab-info" class="hidden">
    <div class="max-w-container-max-width mx-auto px-4 md:px-card-padding py-8 space-y-6">
        <h2 class="text-headline-md font-display-lg text-on-surface">Informasi Platform</h2>
        <div class="bg-surface rounded-2xl border border-outline-variant/40 p-6 text-sm text-on-surface-variant leading-relaxed space-y-3">
            <p>SensorKita adalah platform pemantauan kualitas lingkungan berbasis sensor IoT untuk wilayah Kabupaten Tangerang.</p>
            <p>Data sensor diambil secara real-time dari perangkat Smart Citizen yang terpasang di lapangan, dan dikombinasikan dengan prakiraan cuaca BMKG untuk menghasilkan matriks risiko kesehatan lingkungan.</p>
            <p>Platform ini dikembangkan oleh <strong class="text-on-surface">Bengkel Udara Community</strong> sebagai bagian dari program ketahanan lingkungan berbasis komunitas.</p>
        </div>
    </div>
</div> --}}

<div id="tab-info" class="hidden">

    <div class="max-w-container-max-width mx-auto px-4 md:px-card-padding py-8 space-y-10">
 
        {{-- ============================================= --}}
        {{-- HERO --}}
        {{-- ============================================= --}}
        <section class="relative bg-surface-container rounded-3xl border border-outline-variant/40 p-6 sm:p-8 flex items-start justify-between gap-6 overflow-hidden">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 bg-surface text-primary border border-outline-variant/50 rounded-full px-3 py-1 text-xs font-label-caps mb-4">
                    <span class="material-symbols-outlined text-sm">menu_book</span> Panduan Lapangan
                </span>
 
                <h1 class="text-headline-md font-display-lg text-on-surface mb-3">
                    Interpretasi Sensor BME680
                </h1>
 
                <p class="text-sm sm:text-base text-on-surface-variant leading-relaxed">
                    Panduan praktis untuk memahami dinamika sirkulasi udara, kelembapan tropis, dan indeks
                    kesegaran lingkungan di dalam rumah tangga Indonesia.
                </p>
            </div>
 
            <div class="hidden sm:flex w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border border-outline-variant/50 bg-surface/60 items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-3xl sm:text-4xl text-on-surface-variant">monitoring</span>
            </div>
        </section>
 
        {{-- ============================================= --}}
        {{-- KARAKTERISTIK SINYAL DASAR --}}
        {{-- ============================================= --}}
        <section>
            <div class="flex items-center gap-3 mb-5">
                <span class="w-1 h-5 bg-primary rounded-full"></span>
                <h2 class="text-lg sm:text-xl font-bold text-on-surface">Karakteristik Sinyal Dasar</h2>
            </div>
 
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="bg-surface rounded-2xl border border-outline-variant/40 shadow-sm p-5 sm:p-6 flex gap-4 items-start">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-secondary text-xl">air</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-on-surface text-sm sm:text-base mb-2">Dinamika Kelembapan vs VOC</h3>
                        <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                            <strong class="text-on-surface">Kelembapan</strong> bersifat mutlak untuk memantau kenyamanan dan jamur.
                            Sementara <strong class="text-on-surface">VOC</strong> merupakan indeks kesegaran udara relatif. Perhatikan
                            lonjakan tajam terhadap garis dasar (<em>baseline</em>), bukan angka absolutnya, karena ia merespons
                            aktivitas seperti memasak atau sirkulasi udara.
                        </p>
                    </div>
                </div>
 
                <div class="bg-surface rounded-2xl border border-outline-variant/40 shadow-sm p-5 sm:p-6 flex gap-4 items-start">
                    <div class="w-10 h-10 rounded-xl bg-error-container flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-error text-xl">warning</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-on-surface text-sm sm:text-base mb-2">Efek Pemanasan Sendiri (Self-Heating)</h3>
                        <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                            Chip BME680 yang berada dekat dengan modul ESP32 dapat membaca suhu
                            <strong class="text-error">1–2°C lebih tinggi</strong> akibat rambatan panas papan pengontrol.
                            Pastikan penempatan node memiliki aliran udara bebas dan terapkan nilai kompensasi (<em>offset</em>)
                            pada firmware Anda.
                        </p>
                    </div>
                </div>
            </div>
        </section>
 
        {{-- ============================================= --}}
        {{-- MATRIKS KONDISI RUMAH TANGGA INDONESIA --}}
        {{-- ============================================= --}}
        <section>
            <div class="flex items-center gap-3 mb-2">
                <span class="w-1 h-5 bg-primary rounded-full"></span>
                <h2 class="text-lg sm:text-xl font-bold text-on-surface">Matriks Kondisi Rumah Tangga Indonesia</h2>
            </div>
            <p class="text-sm text-on-surface-variant mb-5 pl-4">
                Ambang batas acuan lokal untuk mengidentifikasi anomali pada grafik pemantauan Anda:
            </p>
 
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-surface rounded-2xl border border-outline-variant/40 shadow-sm p-5 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-10 h-10 rounded-full bg-error-container flex items-center justify-center">
                            <span class="material-symbols-outlined text-error text-lg">bug_report</span>
                        </div>
                        <span class="bg-error-container text-error text-[10px] font-label-caps rounded-full px-2.5 py-1">> 70% RH</span>
                    </div>
                    <h3 class="font-bold text-on-surface text-sm sm:text-base mb-2">Risiko Jamur & Dinding Apak</h3>
                    <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                        Kelembapan relatif tinggi yang menetap dalam waktu lama (biasanya pasca hujan atau malam hari)
                        memicu spora jamur pada dinding rumah.
                    </p>
                </div>
 
                <div class="bg-surface rounded-2xl border border-outline-variant/40 shadow-sm p-5 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-lg">bed</span>
                        </div>
                        <span class="bg-secondary-container text-on-secondary-container text-[10px] font-label-caps rounded-full px-2.5 py-1">> 60% RH</span>
                    </div>
                    <h3 class="font-bold text-on-surface text-sm sm:text-base mb-2">Alergen & Tungau Debu</h3>
                    <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                        Kondisi kasur atau ruangan yang konsisten lembap menjadi lingkungan ideal bagi
                        perkembangbiakan tungau pemicu alergi pernapasan.
                    </p>
                </div>
 
                <div class="bg-surface rounded-2xl border border-outline-variant/40 shadow-sm p-5 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-10 h-10 rounded-full bg-primary-container/30 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-lg">water_drop</span>
                        </div>
                        <span class="bg-primary-container/40 text-primary text-[10px] font-label-caps rounded-full px-2.5 py-1">Info Tekanan</span>
                    </div>
                    <h3 class="font-bold text-on-surface text-sm sm:text-base mb-2">Siklus Genangan & Nyamuk</h3>
                    <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                        Kombinasi jatuhnya tekanan udara dan melonjaknya kelembapan mengindikasikan datangnya
                        hujan besar. Alarm dini untuk waspada genangan air.
                    </p>
                </div>
            </div>
        </section>
 
        {{-- ============================================= --}}
        {{-- MODUL EKSTENSI MASA DEPAN --}}
        {{-- ============================================= --}}
        <section class="bg-surface rounded-3xl border border-outline-variant/40 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-outline-variant/40">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-on-surface-variant">extension</span>
                    <h2 class="font-bold text-on-surface text-base sm:text-lg">Modul Ekstensi Masa Depan</h2>
                </div>
                <p class="text-xs sm:text-sm text-on-surface-variant">
                    Kit dasar mengukur indeks campuran tunggal (VOC relatif). Pasang modul tambahan berikut
                    jika Anda memerlukan metrik spesifik:
                </p>
            </div>
 
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[600px]">
                    <thead class="bg-surface-container-low text-on-surface-variant text-xs font-label-caps">
                        <tr>
                            <th class="px-5 sm:px-6 py-3 text-left">Target Parameter</th>
                            <th class="px-5 sm:px-6 py-3 text-left">Keterbatasan Kit Dasar</th>
                            <th class="px-5 sm:px-6 py-3 text-left">Rekomendasi Modul Tambahan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        <tr>
                            <td class="px-5 sm:px-6 py-4 font-bold text-on-surface align-top">Kadar CO₂ Murni / Laju Ventilasi</td>
                            <td class="px-5 sm:px-6 py-4 text-on-surface-variant align-top">
                                Indeks VOC hanya mendeteksi perubahan bau/gas, bukan volume sirkulasi udara aktual.
                            </td>
                            <td class="px-5 sm:px-6 py-4 align-top">
                                <span class="inline-block bg-surface-container-low border border-outline-variant rounded-md px-2.5 py-1.5 text-xs font-mono text-on-surface-variant whitespace-nowrap">
                                    Sensirion SCD41 (I2C)
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-5 sm:px-6 py-4 font-bold text-on-surface align-top">Partikulat Debu PM2.5 / Polusi Asap</td>
                            <td class="px-5 sm:px-6 py-4 text-on-surface-variant align-top">
                                Tidak memiliki komponen optik penangkap partikulat mikroskopis di udara.
                            </td>
                            <td class="px-5 sm:px-6 py-4 align-top">
                                <span class="inline-block bg-surface-container-low border border-outline-variant rounded-md px-2.5 py-1.5 text-xs font-mono text-on-surface-variant whitespace-nowrap">
                                    Plantower PMS7003T (UART)
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
 
    </div>
</div>


@endsection

@push('extra-scripts')
{{-- Leaflet sudah di-load di app.blade.php, jangan load lagi --}}
<script src="{{ asset('js/dashboard.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>
@endpush
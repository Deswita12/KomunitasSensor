<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'SensorKita Tangerang')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    @stack('extra-head')
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: { extend: {
                colors: {
                    "secondary": "#695d46", "surface-dim": "#dcd9d9",
                    "surface-container": "#f0eded", "on-error-container": "#93000a",
                    "primary": "#416744", "outline": "#727970",
                    "on-secondary-container": "#6d614a", "primary-fixed-dim": "#a7d1a6",
                    "inverse-primary": "#a7d1a6", "tertiary-container": "#73a4a6",
                    "inverse-on-surface": "#f3f0f0", "on-secondary-fixed-variant": "#504530",
                    "on-surface-variant": "#424941", "surface-container-lowest": "#ffffff",
                    "secondary-fixed": "#f2e0c3", "on-primary-fixed-variant": "#294f2e",
                    "primary-container": "#7da67e", "outline-variant": "#c2c8be",
                    "on-secondary": "#ffffff", "surface-container-low": "#f6f3f2",
                    "surface-bright": "#fbf9f8", "on-surface": "#1b1c1c",
                    "surface-variant": "#e4e2e1", "on-error": "#ffffff",
                    "background": "#fbf9f8", "surface-container-high": "#eae8e7",
                    "on-tertiary": "#ffffff", "surface": "#fbf9f8",
                    "on-primary": "#ffffff", "on-primary-container": "#153b1c",
                    "primary-fixed": "#c2eec1", "inverse-surface": "#303030",
                    "secondary-container": "#efdec0", "on-background": "#1b1c1c",
                    "surface-container-highest": "#e4e2e1", "error": "#ba1a1a",
                    "tertiary": "#356668", "surface-tint": "#416744",
                    "error-container": "#ffdad6", "secondary-fixed-dim": "#d5c5a8",
                    "on-tertiary-fixed-variant": "#1a4e50", "on-secondary-fixed": "#231a08",
                    "on-primary-fixed": "#002108"
                },
                borderRadius: {"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},
                spacing: {"base":"8px","container-max-width":"1140px","card-padding":"24px","section-gap-mobile":"40px","section-gap-desktop":"80px"},
                fontFamily: {"display-lg-mobile":["Plus Jakarta Sans"],"display-lg":["Plus Jakarta Sans"],"body-lg":["Inter"],"headline-md":["Plus Jakarta Sans"],"body-md":["Inter"],"label-caps":["Inter"]},
                fontSize: {
                    "display-lg-mobile":["32px",{"lineHeight":"1.2","fontWeight":"700"}],
                    "display-lg":["48px",{"lineHeight":"1.2","letterSpacing":"-0.02em","fontWeight":"700"}],
                    "body-lg":["18px",{"lineHeight":"1.7","fontWeight":"400"}],
                    "headline-md":["24px",{"lineHeight":"1.4","fontWeight":"600"}],
                    "body-md":["16px",{"lineHeight":"1.6","fontWeight":"400"}],
                    "label-caps":["14px",{"lineHeight":"1.2","letterSpacing":"0.05em","fontWeight":"600"}]
                }
            }}
        }
    </script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <script>
        (function () {
            if (localStorage.getItem('sk-theme') === 'dark') {
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-background text-on-background font-body-md overflow-x-hidden">

    {{-- Navbar --}}
    <header class="bg-surface sticky top-0 z-50 shadow-sm w-full h-16 flex items-center">
        <div class="w-full max-w-container-max-width mx-auto px-4 md:px-card-padding flex justify-between items-center relative">
            <div class="flex items-center gap-2">
                <span class="font-display-lg text-headline-md text-primary tracking-tight cursor-pointer"
                      onclick="window.location.href='{{ route('home') }}'">SensorKita</span>
            </div>

            <nav class="hidden md:flex items-center gap-6">
                <div class="relative group">
                    <button id="desktop-data-btn" class="font-label-caps text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 py-2">
                        Home <span class="material-symbols-outlined text-sm transition-transform duration-200" id="data-arrow">keyboard_arrow_down</span>
                    </button>
                    <div id="desktop-dropdown" class="absolute left-0 mt-1 w-48 bg-surface rounded-xl shadow-lg border border-outline-variant py-2 hidden z-50">
                        <button onclick="window.location.href='{{ route('data.dashboard') }}'" class="w-full text-left px-4 py-2 text-sm font-label-caps text-on-surface-variant hover:bg-surface-container hover:text-primary transition-all">Dashboard</button>
                        <button onclick="switchSubTab('info')" class="w-full text-left px-4 py-2 text-sm font-label-caps text-primary hover:bg-surface-container transition-all">Info</button>
                    </div>
                </div>
                <a class="font-label-caps {{ request()->routeIs('panduan') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }} py-1 transition-colors"
                   href="{{ route('panduan') }}">Panduan</a>
                <a class="font-label-caps {{ request()->routeIs('komunitas') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }} py-1 transition-colors"
                   href="{{ route('komunitas') }}">Komunitas</a>
            </nav>

            <div class="flex items-center gap-2 md:gap-4">
                <a href="{{ route('panduan') }}#faq" class="material-symbols-outlined text-on-surface-variant cursor-pointer p-1 rounded-full hover:bg-surface-container transition-colors" title="FAQ">help</a>
                <span onclick="openSettings()" class="material-symbols-outlined text-on-surface-variant cursor-pointer p-1 rounded-full hover:bg-surface-container transition-colors">settings</span>
                <button id="hamburger-btn" class="md:hidden flex items-center p-1 rounded-full text-on-surface-variant hover:bg-surface-container transition-colors" aria-label="Toggle menu" aria-expanded="false">
                    <span class="material-symbols-outlined" id="hamburger-icon">menu</span>
                </button>
                <a href="https://wa.me/6287775881409" target="_blank" rel="noopener noreferrer" class="hidden md:flex items-center gap-1 px-3 py-1.5 rounded-full bg-primary text-on-primary hover:opacity-90 transition-opacity text-sm font-label-caps">
                    <span class="material-symbols-outlined text-base">chat</span><span>Hubungi Kami</span>
                </a>
            </div>

            {{-- Mobile menu --}}
            <div id="mobile-menu" class="absolute top-16 left-0 right-0 bg-surface border-b border-outline-variant shadow-md p-4 hidden flex-col gap-4 md:hidden z-50">
                <div>
                    <button id="mobile-data-btn" class="w-full flex justify-between items-center font-label-caps text-on-surface-variant py-2">
                        <span>Home</span>
                        <span class="material-symbols-outlined transition-transform" id="mobile-data-arrow">keyboard_arrow_down</span>
                    </button>
                    <div id="mobile-data-sub" class="hidden pl-4 flex flex-col gap-2 mt-1 border-l-2 border-outline-variant">
                        <button onclick="window.location.href='{{ route('data.dashboard') }}'" class="text-left font-label-caps text-sm text-on-surface-variant hover:text-primary py-2">Dashboard</button>
                        <button onclick="switchSubTab('info')" class="w-full text-left px-4 py-2 text-sm font-label-caps text-primary hover:bg-surface-container transition-all">Info</button>
                    </div>
                </div>
                <a class="font-label-caps text-on-surface-variant hover:text-primary py-2" href="{{ route('panduan') }}">Panduan</a>
                <a class="font-label-caps text-on-surface-variant hover:text-primary py-2" href="{{ route('komunitas') }}">Komunitas</a>
            </div>
        </div>
    </header>

    {{-- Konten halaman --}}
    @yield('content')

    {{-- Settings Panel --}}
    <div id="settings-overlay" class="fixed inset-0 bg-black/40 z-[90] hidden" onclick="closeSettings()"></div>
    <div id="settings-panel" class="fixed top-0 right-0 h-full w-80 max-w-full bg-surface shadow-2xl z-[100] translate-x-full transition-transform duration-300 flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
            <span class="font-label-caps text-on-surface text-base">Pengaturan</span>
            <button onclick="closeSettings()" class="p-1 rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
            <div>
                <p class="font-label-caps text-xs text-on-surface-variant mb-3 tracking-widest uppercase">Tema</p>
                <div class="flex gap-3">
                    <button id="theme-light-btn" onclick="setTheme('light')" class="flex-1 flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-primary bg-primary-container/20 transition-all">
                        <span class="material-symbols-outlined text-primary">light_mode</span>
                        <span class="font-label-caps text-xs text-primary">Terang</span>
                    </button>
                    <button id="theme-dark-btn" onclick="setTheme('dark')" class="flex-1 flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-outline-variant hover:border-primary transition-all">
                        <span class="material-symbols-outlined text-on-surface-variant">dark_mode</span>
                        <span class="font-label-caps text-xs text-on-surface-variant">Gelap</span>
                    </button>
                </div>
            </div>
            <div class="border-t border-outline-variant"></div>
            <div>
                <p class="font-label-caps text-xs text-on-surface-variant mb-3 tracking-widest uppercase">Bantuan</p>
                <p class="text-sm text-on-surface-variant mb-4 leading-relaxed">Punya pertanyaan untuk developer?</p>
                <button onclick="openHelpModal()" class="w-full bg-primary text-on-primary font-label-caps py-3 rounded-full hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">chat</span> Bantuan
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Bantuan --}}
    <div id="help-modal-overlay" class="fixed inset-0 bg-black/50 z-[200] hidden items-center justify-center p-4" onclick="closeHelpModal(event)">
        <div class="bg-surface rounded-2xl shadow-2xl w-full max-w-md p-6 flex flex-col gap-4" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between">
                <span class="font-label-caps text-on-surface">Kirim Pertanyaan</span>
                <button onclick="closeHelpModal()" class="p-1 rounded-full hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-on-surface-variant">close</span>
                </button>
            </div>
            <div id="help-form-state" class="flex flex-col gap-3">
                @csrf
                <div>
                    <label class="font-label-caps text-xs text-on-surface-variant mb-1 block">Nama</label>
                    <input id="help-name" type="text" placeholder="Nama kamu" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container text-on-surface text-sm focus:outline-none focus:border-primary transition-colors" />
                </div>
                <div>
                    <label class="font-label-caps text-xs text-on-surface-variant mb-1 block">Email</label>
                    <input id="help-email" type="email" placeholder="email@kamu.com" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container text-on-surface text-sm focus:outline-none focus:border-primary transition-colors" />
                </div>
                <div>
                    <label class="font-label-caps text-xs text-on-surface-variant mb-1 block">Pertanyaan</label>
                    <textarea id="help-message" rows="4" placeholder="Tulis pertanyaanmu di sini..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container text-on-surface text-sm focus:outline-none focus:border-primary transition-colors resize-none"></textarea>
                </div>
                <p id="help-error" class="text-xs text-error hidden"></p>
                <button onclick="submitHelpForm()" class="w-full bg-primary text-on-primary font-label-caps py-3 rounded-full hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">send</span> Kirim Email
                </button>
            </div>
            <div id="help-success-state" class="hidden flex flex-col items-center text-center gap-4 py-4">
                <div class="w-16 h-16 rounded-full bg-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl text-primary">check_circle</span>
                </div>
                <div>
                    <p class="font-label-caps text-on-surface mb-1">Pesan Terkirim!</p>
                    <p class="text-sm text-on-surface-variant">Developer akan merespons ke email kamu segera.</p>
                </div>
                <button onclick="resetHelpForm()" class="border border-outline-variant text-on-surface-variant font-label-caps text-sm px-6 py-2 rounded-full hover:bg-surface-container transition-colors">Tutup</button>
            </div>
        </div>
    </div>

    <footer class="bg-surface-container-highest border-t border-outline-variant mt-section-gap-desktop">
        <div class="max-w-container-max-width mx-auto py-8 px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-left">
                <span class="font-headline-md text-secondary">SensorKita</span>
                <p class="font-label-caps text-xs text-on-surface-variant mt-1">© {{ date('Y') }} Bengkel Udara Community</p>
            </div>
            <div class="flex flex-wrap justify-center gap-4 sm:gap-6">
                <a class="font-label-caps text-xs sm:text-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Kebijakan Privasi</a>
                <a class="font-label-caps text-xs sm:text-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Kontak Kami</a>
            </div>
        </div>
    </footer>

    <script>
        // const csrfToken = '{{ csrf_token() }}';
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    @stack('extra-scripts')

    
</body>
</html>
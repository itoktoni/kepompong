@php
    use App\Models\Plan;
    $plans = Plan::where('plan_status', 1)->orderBy('plan_harga')->get();
@endphp
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kepompong - Solusi Ide Bermain & Tracking Karakter Anak</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#176c33",
                        "primary-container": "#6dbe7b",
                        "on-primary": "#ffffff",
                        "on-primary-container": "#004b1e",
                        "leaf-deep": "#176c33",
                        "warm-amber": "#f59e0b",
                        "soft-border": "#e5e7eb",
                        surface: "#fef8f2",
                        "surface-bright": "#fef8f2",
                        "surface-container": "#f3ede7",
                        "surface-container-low": "#f8f3ed",
                        "surface-container-high": "#ede7e1",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#1d1b18",
                        "on-surface-variant": "#40493f",
                        "outline-variant": "#bfc9bc",
                        outline: "#707a6e",
                        "secondary": "#56615a",
                        "secondary-container": "#d9e6dc",
                        "tertiary": "#0061a4",
                        "tertiary-container": "#67b1ff",
                        error: "#ba1a1a",
                        "error-container": "#ffdad6",
                    },
                    fontFamily: {
                        quicksand: ["Quicksand", "sans-serif"],
                        fredoka: ["Fredoka", "sans-serif"],
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #fef8f2;
            color: #1d1b18;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .bento-card {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .bento-card:hover {
            transform: translateY(-4px);
        }
        .scroll-reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        .butterfly-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
        }
        .faq-content.open {
            max-height: 500px;
        }
        .testimonial-track {
            transition: transform 0.5s ease-in-out;
        }
        @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden">
    {{-- TopNavBar --}}
    <header class="bg-surface-bright/80 sticky top-0 backdrop-blur-md border-b border-soft-border z-50">
        <nav class="flex justify-between items-center w-full px-4 md:px-16 py-4 max-w-[1140px] mx-auto">
            <a href="/" class="font-bold text-2xl text-leaf-deep font-fredoka">Kepompong</a>
            <div class="hidden md:flex gap-8 items-center">
                <a class="text-sm font-semibold text-primary border-b-2 border-primary pb-1" href="#">Mission</a>
                <a class="text-sm font-semibold text-on-surface-variant hover:text-primary transition-colors" href="#metamorphosis">Metamorphosis</a>
                <a class="text-sm font-semibold text-on-surface-variant hover:text-primary transition-colors" href="#pricing">Pricing</a>
                <a class="text-sm font-semibold text-on-surface-variant hover:text-primary transition-colors" href="#faq">FAQ</a>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-primary text-white px-6 py-2 rounded-full text-sm font-semibold hover:opacity-90 active:scale-95 transition-all">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-on-surface-variant hover:text-primary transition-colors">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-primary text-white px-6 py-2 rounded-full text-sm font-semibold hover:opacity-90 active:scale-95 transition-all">Get Started</a>
                    @endif
                @endauth
            </div>
        </nav>
    </header>

    <main>
        {{-- Hero Section --}}
        <section class="relative px-4 md:px-16 pt-16 pb-24 max-w-[1200px] mx-auto overflow-hidden"
            x-data="{
                active: 0,
                slides: [
                    {
                        badge: 'Sahabat Parenting Modern',
                        title: 'Solusi 3 Detik Ide Bermain & <span class=\'text-primary\'>Tracking Karakter Anak</span>',
                        desc: 'Ubah waktu layar menjadi waktu berkualitas. Dapatkan ide aktivitas kreatif instan dan pantau perkembangan soft skill si kecil setiap hari.'
                    },
                    {
                        badge: 'Metamorfosis Karakter',
                        title: 'Dari Ulat Menjadi <span class=\'text-primary\'>Kupu-Kupu Karakter</span> yang Indah',
                        desc: 'Pantau perjalanan karakter anak melalui 4 tahap metamorfosis: Egg, Caterpillar, Chrysalis, dan Butterfly secara visual dan terukur.'
                    },
                    {
                        badge: '15.000+ Keluarga Bahagia',
                        title: 'Worksheet Kreatif & <span class=\'text-primary\'>Aktivitas Offline</span> Setiap Hari',
                        desc: 'Ratusan worksheet siap cetak dan ide bermain yang disesuaikan dengan usia anak. Tanpa gadget, penuh makna.'
                    }
                ],
                autoplay() {
                    setInterval(() => { this.active = (this.active + 1) % this.slides.length }, 4000)
                }
            }" x-init="autoplay()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="z-10">
                    <template x-for="(slide, i) in slides" :key="i">
                        <div x-show="active === i"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-4"
                            class="absolute">
                            <span class="inline-block px-4 py-1 rounded-full bg-primary-container/30 text-on-primary-container font-semibold text-sm mb-6" x-text="slide.badge"></span>
                            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6 text-on-surface" x-html="slide.title"></h1>
                            <p class="text-lg text-on-surface-variant mb-10 max-w-lg leading-relaxed" x-text="slide.desc"></p>
                        </div>
                    </template>
                    {{-- Spacer to reserve height for absolute slides --}}
                    <div class="invisible">
                        <span class="inline-block px-4 py-1 rounded-full text-sm mb-6">&nbsp;</span>
                        <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">&nbsp;<br>&nbsp;</h1>
                        <p class="text-lg mb-10">&nbsp;<br>&nbsp;</p>
                    </div>
                    {{-- Slider dots --}}
                    <div class="flex gap-2 mb-6">
                        <template x-for="(_, i) in slides" :key="i">
                            <button @click="active = i" class="h-1.5 rounded-full transition-all duration-300" :class="active === i ? 'bg-primary w-8' : 'bg-outline-variant w-4 hover:bg-outline'"></button>
                        </template>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-primary text-white px-8 py-4 rounded-2xl text-lg font-bold shadow-lg hover:shadow-xl transition-all text-center">
                                Masuk Dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-primary text-white px-8 py-4 rounded-2xl text-lg font-bold shadow-lg hover:shadow-xl transition-all text-center">
                                    Coba Sekarang
                                </a>
                            @endif
                        @endauth
                        <a href="#metamorphosis" class="bg-surface-container-high text-on-surface px-8 py-4 rounded-2xl text-lg font-bold border border-outline-variant hover:bg-surface-variant transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">play_circle</span> Lihat Cara Kerja
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-tertiary-container/20 rounded-full blur-3xl"></div>
                    <div class="rounded-3xl overflow-hidden shadow-2xl relative border-8 border-white">
                        <img class="w-full h-[500px] object-cover"
                            alt="Parent and child playing together"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDe4KCuZnhuIPJup87HeB8_AdMOincQ-ZdAJw6RraeaEFbT82h3O4VX9ydZxsvC-u_jcm9rzk02dWH1GymMJhJFIf34LqlL10DdT4IXWxIAzNU4kz63TgWKySyH_lDDkbt_mga1HD-Acwx5vbljoVCSGwHXakHDcMk2e5QLXkR8xs0LKL1jnYTz8CbLUl6tnMdCIoAvtvKn_kyNWRMGgBXls6LVcDoHlQOiYJthnBhKMP7X_w4TWWfUBZW2cVqFNroVTk80y-tftih8" />
                    </div>
                </div>
            </div>
        </section>

        {{-- Features Slider --}}
        <section class="bg-surface-container-low py-24">
            <div class="max-w-[1200px] mx-auto px-4 md:px-16">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">Fitur Utama Kepompong</h2>
                    <p class="text-lg text-on-surface-variant">Lengkapi perjalanan parenting Bunda dengan alat yang tepat.</p>
                </div>
                <div x-data="{ active: 0, total: 3 }" class="relative">
                    <div class="overflow-hidden rounded-2xl">
                        <div class="testimonial-track flex" :style="'transform: translateX(-' + (active * 100) + '%)'">
                            {{-- Feature 1 --}}
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="bg-white p-8 rounded-2xl shadow-sm border border-outline-variant flex flex-col md:flex-row gap-8 overflow-hidden">
                                    <div class="flex-1">
                                        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">bolt</span>
                                        </div>
                                        <h3 class="text-xl font-bold mb-4">Ide Bermain Instan</h3>
                                        <p class="text-on-surface-variant leading-relaxed">Algoritma 3 detik kami menyesuaikan usia anak dan bahan yang ada di rumah untuk memberikan rekomendasi aktivitas offline terbaik.</p>
                                    </div>
                                    <div class="flex-1 bg-surface-container rounded-xl p-4 flex items-center justify-center">
                                        <div class="space-y-3 w-full">
                                            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-primary flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary">auto_awesome</span>
                                                <span class="font-semibold text-sm">Melukis dengan Es Batu</span>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-tertiary flex items-center gap-3 opacity-60">
                                                <span class="material-symbols-outlined text-tertiary">science</span>
                                                <span class="font-semibold text-sm">Eksperimen Gunung Berapi</span>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-warm-amber flex items-center gap-3 opacity-30">
                                                <span class="material-symbols-outlined text-warm-amber">theater_comedy</span>
                                                <span class="font-semibold text-sm">Panggung Boneka Jari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Feature 2 --}}
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="bg-primary text-white p-8 rounded-2xl shadow-lg flex flex-col md:flex-row gap-8 items-center">
                                    <div class="flex-1">
                                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">insights</span>
                                        </div>
                                        <h3 class="text-xl font-bold mb-4">Pelacak Karakter</h3>
                                        <p class="opacity-90 leading-relaxed mb-6">Pantau progress soft skill: Jujur, Disiplin, dan Mandiri secara visual dan terukur melalui grafik interaktif.</p>
                                        <div class="space-y-3">
                                            <div>
                                                <div class="flex justify-between text-sm mb-1"><span>Mandiri</span><span>85%</span></div>
                                                <div class="w-full bg-white/20 rounded-full h-2"><div class="bg-white h-full rounded-full" style="width: 85%"></div></div>
                                            </div>
                                            <div>
                                                <div class="flex justify-between text-sm mb-1"><span>Disiplin</span><span>72%</span></div>
                                                <div class="w-full bg-white/20 rounded-full h-2"><div class="bg-white h-full rounded-full" style="width: 72%"></div></div>
                                            </div>
                                            <div>
                                                <div class="flex justify-between text-sm mb-1"><span>Jujur</span><span>90%</span></div>
                                                <div class="w-full bg-white/20 rounded-full h-2"><div class="bg-white h-full rounded-full" style="width: 90%"></div></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-center justify-center">
                                        <div class="w-48 h-48 rounded-full bg-white/10 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-6xl" style="font-variation-settings: 'FILL' 1;">emoji_events</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Feature 3 --}}
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="bg-surface-container-high p-8 rounded-2xl border border-outline-variant flex flex-col md:flex-row items-center gap-8">
                                    <div class="flex gap-4 order-2 md:order-1">
                                        <div class="w-28 h-40 bg-white rounded-lg shadow-md border-2 border-dashed border-outline transform -rotate-6 hover:rotate-0 transition-transform"></div>
                                        <div class="w-28 h-40 bg-white rounded-lg shadow-md border-2 border-dashed border-outline z-10 hover:scale-105 transition-transform"></div>
                                        <div class="w-28 h-40 bg-white rounded-lg shadow-md border-2 border-dashed border-outline transform rotate-6 hover:rotate-0 transition-transform"></div>
                                    </div>
                                    <div class="order-1 md:order-2 flex-1">
                                        <div class="w-12 h-12 bg-warm-amber/10 rounded-xl flex items-center justify-center mb-6">
                                            <span class="material-symbols-outlined text-warm-amber" style="font-variation-settings: 'FILL' 1;">description</span>
                                        </div>
                                        <h3 class="text-xl font-bold mb-4">Worksheet Siap Cetak</h3>
                                        <p class="text-on-surface-variant leading-relaxed">Ratusan materi edukasi mulai dari mewarnai hingga problem solving yang bisa Bunda download dan print kapan saja.</p>
                                        <button class="mt-6 text-primary font-bold flex items-center gap-2 hover:gap-3 transition-all">
                                            Jelajahi Perpustakaan <span class="material-symbols-outlined">arrow_forward</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Slider Controls --}}
                    <div class="flex items-center justify-center gap-4 mt-8">
                        <button @click="active = active > 0 ? active - 1 : total - 1" class="w-10 h-10 rounded-full bg-white border border-outline-variant flex items-center justify-center hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <div class="flex gap-2">
                            <template x-for="i in total" :key="i">
                                <button @click="active = i - 1" class="w-3 h-3 rounded-full transition-all" :class="active === i - 1 ? 'bg-primary w-8' : 'bg-outline-variant'"></button>
                            </template>
                        </div>
                        <button @click="active = active < total - 1 ? active + 1 : 0" class="w-10 h-10 rounded-full bg-white border border-outline-variant flex items-center justify-center hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- Metamorphosis Steps --}}
        <section id="metamorphosis" class="py-24 px-4 md:px-16 max-w-[1200px] mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Perjalanan Metamorfosis Si Kecil</h2>
                <p class="text-lg text-on-surface-variant max-w-2xl mx-auto">Dari ulat yang lapar akan ilmu menjadi kupu-kupu karakter yang indah.</p>
            </div>
            <div class="relative">
                <div class="hidden md:block absolute top-12 left-0 w-full h-0.5 -translate-y-1/2 z-0" style="background-image: linear-gradient(to right, #bfc9bc 50%, rgba(255,255,255,0) 0%); background-position: bottom; background-size: 15px 1px; background-repeat: repeat-x;"></div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    {{-- Step 1 --}}
                    <div class="relative bg-surface p-6 rounded-2xl text-center z-10 border border-outline-variant md:border-transparent scroll-reveal">
                        <div class="w-20 h-20 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-primary text-5xl">egg</span>
                        </div>
                        <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center mx-auto -mt-10 mb-4 relative z-10 text-sm font-bold shadow-lg">1</div>
                        <h4 class="text-xl font-bold mb-2">The Egg</h4>
                        <p class="text-sm font-semibold text-on-primary-container mb-2">Initial Assessment</p>
                        <p class="text-on-surface-variant text-sm">Daftarkan profil dan minat unik si kecil untuk memulai perjalanan.</p>
                    </div>
                    {{-- Step 2 --}}
                    <div class="relative bg-surface p-6 rounded-2xl text-center z-10 border border-outline-variant md:border-transparent scroll-reveal" style="transition-delay: 100ms;">
                        <div class="w-20 h-20 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-primary text-5xl">bug_report</span>
                        </div>
                        <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center mx-auto -mt-10 mb-4 relative z-10 text-sm font-bold shadow-lg">2</div>
                        <h4 class="text-xl font-bold mb-2">Caterpillar</h4>
                        <p class="text-sm font-semibold text-on-primary-container mb-2">Character Building</p>
                        <p class="text-on-surface-variant text-sm">Dapatkan ide bermain personal dalam 3 detik sesuai usia anak.</p>
                    </div>
                    {{-- Step 3 --}}
                    <div class="relative bg-surface p-6 rounded-2xl text-center z-10 border border-outline-variant md:border-transparent scroll-reveal" style="transition-delay: 200ms;">
                        <div class="w-20 h-20 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-primary text-5xl">potted_plant</span>
                        </div>
                        <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center mx-auto -mt-10 mb-4 relative z-10 text-sm font-bold shadow-lg">3</div>
                        <h4 class="text-xl font-bold mb-2">Chrysalis</h4>
                        <p class="text-sm font-semibold text-on-primary-container mb-2">Offline Play</p>
                        <p class="text-on-surface-variant text-sm">Waktunya bermain nyata tanpa gangguan gadget bersama keluarga.</p>
                    </div>
                    {{-- Step 4 --}}
                    <div class="relative bg-surface p-6 rounded-2xl text-center z-10 border border-outline-variant md:border-transparent scroll-reveal" style="transition-delay: 300ms;">
                        <div class="w-20 h-20 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-primary text-5xl">flutter_dash</span>
                        </div>
                        <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center mx-auto -mt-10 mb-4 relative z-10 text-sm font-bold shadow-lg">4</div>
                        <h4 class="text-xl font-bold mb-2">Butterfly</h4>
                        <p class="text-sm font-semibold text-on-primary-container mb-2">Assessment</p>
                        <p class="text-on-surface-variant text-sm">Input hasil bermain & lihat grafik perkembangan karakter anak.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Testimonial Slider --}}
        <section class="py-24 bg-surface-container-low">
            <div class="max-w-[1200px] mx-auto px-4 md:px-16">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">Bergabung dengan <span class="text-primary">15.000+ Keluarga</span> Bahagia</h2>
                    <p class="text-lg text-on-surface-variant">Kepompong bukan sekadar aplikasi, tapi komunitas orang tua yang peduli.</p>
                </div>
                <div x-data="{ active: 0, total: 4, autoplay() { setInterval(() => { this.active = (this.active + 1) % this.total }, 5000) } }" x-init="autoplay()" class="relative">
                    <div class="overflow-hidden rounded-2xl">
                        <div class="testimonial-track flex" :style="'transform: translateX(-' + (active * 100) + '%)'">
                            {{-- Testimonial 1 --}}
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="bg-white p-10 rounded-3xl shadow-sm border border-outline-variant flex flex-col md:flex-row gap-8 items-center">
                                    <div class="flex-1">
                                        <span class="material-symbols-outlined text-primary-container text-6xl mb-4 opacity-40" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                        <p class="italic text-xl mb-8 leading-relaxed">"Kepompong bener-bener penyelamat saat aku kehabisan ide main sama si kecil. Tracking karakternya bikin aku lebih aware sama perkembangan disiplinnya yang makin hari makin oke!"</p>
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold">M</div>
                                            <div>
                                                <div class="font-bold">Bunda Maya</div>
                                                <div class="text-sm text-on-surface-variant">Ibu dari Rayyan (4th)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-48 h-48 rounded-2xl bg-surface-container flex items-center justify-center">
                                        <span class="material-symbols-outlined text-6xl text-primary/20">family_restroom</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Testimonial 2 --}}
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="bg-white p-10 rounded-3xl shadow-sm border border-outline-variant flex flex-col md:flex-row gap-8 items-center">
                                    <div class="flex-1">
                                        <span class="material-symbols-outlined text-primary-container text-6xl mb-4 opacity-40" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                        <p class="italic text-xl mb-8 leading-relaxed">"Worksheet-nya sangat membantu! Anak saya jadi punya kegiatan positif setiap hari. Yang paling senang, dia nggak minta main HP lagi karena asik dengan aktivitas dari Kepompong."</p>
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-tertiary-container flex items-center justify-center text-on-tertiary-container font-bold">S</div>
                                            <div>
                                                <div class="font-bold">Bunda Sari</div>
                                                <div class="text-sm text-on-surface-variant">Ibu dari Aisha (5th)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-48 h-48 rounded-2xl bg-surface-container flex items-center justify-center">
                                        <span class="material-symbols-outlined text-6xl text-primary/20">school</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Testimonial 3 --}}
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="bg-white p-10 rounded-3xl shadow-sm border border-outline-variant flex flex-col md:flex-row gap-8 items-center">
                                    <div class="flex-1">
                                        <span class="material-symbols-outlined text-primary-container text-6xl mb-4 opacity-40" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                        <p class="italic text-xl mb-8 leading-relaxed">"Sebagai ayah yang sibuk kerja, Kepompong bantu saya punya quality time yang berkualitas sama anak. Ide-idenya kreatif dan mudah diikuti. Highly recommended!"</p>
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container font-bold">R</div>
                                            <div>
                                                <div class="font-bold">Pak Rizky</div>
                                                <div class="text-sm text-on-surface-variant">Ayah dari Danu (3th)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-48 h-48 rounded-2xl bg-surface-container flex items-center justify-center">
                                        <span class="material-symbols-outlined text-6xl text-primary/20">sentiment_very_satisfied</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Testimonial 4 --}}
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="bg-white p-10 rounded-3xl shadow-sm border border-outline-variant flex flex-col md:flex-row gap-8 items-center">
                                    <div class="flex-1">
                                        <span class="material-symbols-outlined text-primary-container text-6xl mb-4 opacity-40" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                        <p class="italic text-xl mb-8 leading-relaxed">"Grafik metamorfosisnya bikin anak saya excited! Dia bilang dia mau jadi kupu-kupu. Sekarang dia rajin banget ikutin aktivitas dari Kepompong supaya grafiknya naik terus."</p>
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-warm-amber/20 flex items-center justify-center text-warm-amber font-bold">D</div>
                                            <div>
                                                <div class="font-bold">Bunda Dewi</div>
                                                <div class="text-sm text-on-surface-variant">Ibu dari Kala (6th)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-48 h-48 rounded-2xl bg-surface-container flex items-center justify-center">
                                        <span class="material-symbols-outlined text-6xl text-primary/20">auto_awesome</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Slider Controls --}}
                    <div class="flex items-center justify-center gap-4 mt-8">
                        <button @click="active = active > 0 ? active - 1 : total - 1" class="w-10 h-10 rounded-full bg-white border border-outline-variant flex items-center justify-center hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <div class="flex gap-2">
                            <template x-for="i in total" :key="i">
                                <button @click="active = i - 1" class="w-3 h-3 rounded-full transition-all" :class="active === i - 1 ? 'bg-primary w-8' : 'bg-outline-variant'"></button>
                            </template>
                        </div>
                        <button @click="active = active < total - 1 ? active + 1 : 0" class="w-10 h-10 rounded-full bg-white border border-outline-variant flex items-center justify-center hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- Pricing --}}
        <section id="pricing" class="bg-secondary-container/30 py-24">
            <div class="max-w-[1200px] mx-auto px-4 md:px-16">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">Investasi Untuk Masa Depan</h2>
                    <p class="text-lg text-on-surface-variant">Pilih paket yang paling sesuai dengan kebutuhan stimulasi anak Bunda.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
                    @foreach($plans as $plan)
                        @php
                            $isRecommended = $plan->plan_recomended;
                            $hargaFormatted = 'Rp ' . number_format($plan->plan_harga, 0, ',', '.');
                            $coretFormatted = $plan->plan_coret ? 'Rp ' . number_format($plan->plan_coret, 0, ',', '.') : null;
                            $features = array_filter(array_map('trim', explode("\n", $plan->plan_keterangan ?? '')));
                        @endphp
                        <div class="bg-white p-8 rounded-{{ $isRecommended ? '3xl border-2 border-primary shadow-xl scale-105' : '2xl border border-outline-variant shadow-sm hover:shadow-md' }} transition-shadow relative">
                            @if($isRecommended)
                                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-white px-4 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                    REKOMENDASI BUNDA
                                </div>
                            @endif
                            <h3 class="text-xl font-bold mb-2">{{ $plan->plan_nama }}</h3>
                            <p class="text-sm text-on-surface-variant mb-6">{{ $plan->plan_periode }}</p>
                            <div class="mb-8">
                                <span class="text-3xl font-bold {{ $isRecommended ? 'text-primary' : '' }}">{{ $hargaFormatted }}</span>
                                @if($coretFormatted && $coretFormatted !== $hargaFormatted)
                                    <span class="block text-sm text-on-surface-variant line-through opacity-60">{{ $coretFormatted }}</span>
                                @endif
                            </div>
                            @if(count($features) > 0)
                                <ul class="space-y-4 mb-10">
                                    @foreach($features as $feature)
                                        <li class="flex items-center gap-3">
                                            <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                            <span class="text-sm">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <a href="{{ Route::has('register') ? route('register') : '#' }}" class="block w-full text-center py-{{ $isRecommended ? '4 bg-primary text-white shadow-lg' : '3 border border-primary text-primary hover:bg-primary/5' }} rounded-xl font-bold transition-colors">
                                {{ $isRecommended ? 'Langganan Sekarang' : 'Pilih Paket' }}
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Promo Code CTA --}}
                <div class="mt-16 bg-white p-6 rounded-2xl border-2 border-dashed border-primary max-w-xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <p class="font-bold text-xl">Punya kode promo?</p>
                        <p class="text-on-surface-variant text-sm">Gunakan kode untuk potongan ekstra</p>
                    </div>
                    <div x-data="{ copied: false }" class="flex items-center gap-2">
                        <div class="bg-primary/10 px-6 py-3 rounded-xl text-primary font-bold tracking-widest text-lg">
                            EARLYBUNDA
                        </div>
                        <button @click="navigator.clipboard.writeText('EARLYBUNDA'); copied = true; setTimeout(() => copied = false, 2000)" class="bg-primary text-white p-3 rounded-xl hover:opacity-90 transition-all relative">
                            <span class="material-symbols-outlined" x-text="copied ? 'check' : 'content_copy'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section id="faq" class="py-24 max-w-[1200px] mx-auto px-4 md:px-16">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-lg text-on-surface-variant">Temukan jawaban untuk pertanyaan umum tentang Kepompong.</p>
            </div>
            <div class="max-w-3xl mx-auto space-y-4">
                @php
                    $faqs = [
                        ['q' => 'Apa itu Kepompong?', 'a' => 'Kepompong adalah platform parenting modern yang membantu orang tua memberikan ide aktivitas bermain offline yang disesuaikan dengan usia anak, sekaligus melacak perkembangan karakter dan soft skill si kecil melalui pendekatan metamorfosis.'],
                        ['q' => 'Bagaimana cara kerja ide bermain instan?', 'a' => 'Cukup masukkan usia anak dan bahan yang tersedia di rumah. Algoritma kami akan memberikan rekomendasi aktivitas kreatif dalam hitungan 3 detik yang sudah disesuaikan dengan tahap perkembangan anak Anda.'],
                        ['q' => 'Apa itu tracking karakter metamorfosis?', 'a' => 'Tracking karakter metamorfosis adalah sistem pemantauan perkembangan soft skill anak (seperti jujur, disiplin, mandiri) melalui 4 tahap: Egg (Assessment), Caterpillar (Character Building), Chrysalis (Reflective Practice), dan Butterfly (Resilient & Ready).'],
                        ['q' => 'Apakah semua aktivitas dilakukan offline?', 'a' => 'Ya! Kepompong didesain untuk mengurangi screen time. Semua ide aktivitas dirancang untuk dilakukan secara offline bersama keluarga, menggunakan bahan-bahan yang mudah ditemukan di rumah.'],
                        ['q' => 'Bisakah saya menggunakan Kepompong untuk lebih dari satu anak?', 'a' => 'Tentu! Tergantung paket yang dipilih. Paket Basic untuk 1 anak, Paket Fokus untuk maksimal 2 anak, dan Paket Juara untuk maksimal 5 anak.'],
                        ['q' => 'Bagaimana cara berhenti berlangganan?', 'a' => 'Anda bisa berhenti berlangganan kapan saja melalui halaman pengaturan akun. Akses tetap berlaku hingga akhir periode langganan yang sudah dibayar.'],
                    ];
                @endphp
                @foreach($faqs as $i => $faq)
                    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-outline-variant overflow-hidden">
                        <button @click="open = !open" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-surface-container-low transition-colors">
                            <span class="font-bold pr-4">{{ $faq['q'] }}</span>
                            <span class="material-symbols-outlined text-on-surface-variant flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div class="faq-content" :class="open ? 'open' : ''" :style="open ? 'max-height: 500px; padding: 0 1.5rem 1.5rem;' : 'max-height: 0; padding: 0 1.5rem;'">
                            <p class="text-on-surface-variant leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- CTA Footer --}}
        <section class="py-20 px-4 md:px-16">
            <div class="bg-leaf-deep rounded-[32px] p-12 text-center text-white relative overflow-hidden max-w-[1200px] mx-auto">
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-tertiary-container/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6 relative z-10">Siap Mendampingi Metamorfosis Si Kecil?</h2>
                <p class="text-lg mb-10 opacity-90 max-w-xl mx-auto relative z-10">
                    Mulai perjalanan berharga ini hari ini. Hanya butuh 3 detik untuk ide yang tak terlupakan seumur hidup.
                </p>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-block bg-white text-leaf-deep px-10 py-4 rounded-2xl text-lg font-bold hover:bg-surface-bright transition-all relative z-10">
                        Masuk Dashboard
                    </a>
                @else
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="inline-block bg-white text-leaf-deep px-10 py-4 rounded-2xl text-lg font-bold hover:bg-surface-bright transition-all relative z-10">
                        Mulai Sekarang Gratis
                    </a>
                @endauth
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-surface-container border-t border-outline-variant/30">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8 w-full px-4 md:px-16 py-16 max-w-[1140px] mx-auto">
            <div class="flex flex-col items-center md:items-start gap-4">
                <div class="text-2xl text-leaf-deep font-bold font-fredoka">Kepompong</div>
                <p class="text-on-surface-variant text-sm text-center md:text-left max-w-sm">&copy; {{ date('Y') }} Kepompong Edutech. Nurturing Character, One Offline Moment at a Time.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-8">
                <a class="text-on-surface-variant hover:text-warm-amber transition-colors text-sm" href="#">Privacy Policy</a>
                <a class="text-on-surface-variant hover:text-warm-amber transition-colors text-sm" href="#">Terms of Service</a>
                <a class="text-on-surface-variant hover:text-warm-amber transition-colors text-sm" href="#">Contact Us</a>
                <a class="text-on-surface-variant hover:text-warm-amber transition-colors text-sm" href="#">Press Kit</a>
            </div>
        </div>
    </footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
    </script>
</body>
</html>

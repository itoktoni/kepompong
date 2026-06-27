# Product Requirement Document (PRD): Kepompong App

## 1. Deskripsi Produk

Kepompong adalah aplikasi yang membantu orang tua membentuk soft skill & life skill anak usia golden age 1-10 tahun — karena soft skill dan life skill belum tentu diajarkan secara khusus dan terstruktur di sekolah. Cukup 3 detik untuk mendapatkan rekomendasi aktivitas bermain offline dan lembar kerja (worksheet) terkurasi berdasarkan usia anak dan target karakter yang ingin dilatih, dilengkapi sistem asesmen perilaku berkala.

### 1.1 Masalah yang Dipecahkan

1. Soft skill dan life skill belum diajarkan secara khusus dan terstruktur di sekolah — orang tua perlu jadi guru karakter di rumah.
2. Orang tua kehabisan ide aktivitas untuk anak setiap hari — bingung mau ajak main apa.
3. Aplikasi parenting yang ada sifatnya screen-based (anak belajar dari video/game), bukan offline.
4. Belum ada aplikasi yang menggabungkan rekomendasi aktivitas offline + tracking karakter.
5. Orang tua kesulitan mengukur perkembangan soft skill anak secara objektif — selama ini cuma perasaan.
6. Kekhawatiran screen time pada anak (60% orang tua Milenial/Gen Z batasi screen time).

### 1.2 Blue Ocean Positioning

Kepompong adalah satu-satunya aplikasi di Indonesia yang menggabungkan:
- ✅ Rekomendasi aktivitas OFFLINE instan (3 detik)
- ✅ Assessment soft skill & life skill 5 dimensi berkala
- ✅ Challenge system dengan poin (membangun kebiasaan)
- ✅ Tracking progress visual + jadwal + checklist
- ✅ Worksheet cetak untuk aktivitas offline
- ✅ Untuk orang tua di rumah (bukan sekolah)
- ✅ Usia 1-10 tahun / golden age (kompetitor terdekat Kiddu hanya 4-8)

### 1.3 Target Audiens

**Primer:**
- **Bunda Digital Modern** (Milenial 28-43 th) — S1, urban, percaya pakar. TikTok 33.4%. Prioritas: pendidikan > karakter > kesehatan
- **Bunda Gen Z Awal** (18-27 th) — 71% percaya pakar, 92% riset online lalu beli offline. TikTok 42.27%

**Sekunder:**
- Keluarga homeschooling/flexi-school
- Usia anak: 1-10 tahun (segmentasi 1-3, 4-6, 7-10)

### 1.4 Value Proposition

> "Membantu orang tua membentuk soft skill & life skill anak usia golden age 1-10 tahun dalam 3 detik — karena soft skill belum diajarkan secara khusus di sekolah."

> **Tagline:** "Aktivitas offline untuk soft skill anak — instan, ilmiah, tanpa layar."

---

## 2. Goals & Success Metrics

### 2.1 Business Goals (Launch Year +12 Bulan)

| Kategori | Target | Metrik | Timeline |
|---|---|---|---|
| Total User (Registered) | 10.000 | Signups | Bulan 12 |
| Active Subscribers | 2.500 | Pembayaran sukses | Bulan 12 |
| Monthly Active Users (MAU) | 5.000 | Login ≥2x/bulan | Bulan 12 |
| Retention D7 | ≥50% | Masih aktif hari ke-7 | Stabil |
| Retention D30 | ≥30% | Masih aktif hari ke-30 | Stabil |
| Monthly Recurring Revenue (MRR) | Rp100-150jt | Total subscription revenue | Bulan 12 |
| Churn Rate | <10%/bulan | Pembatalan/langganan aktif | Stabil |

### 2.2 Product Validation Milestones (C1.5 → C2)

| Fase | Aktivitas | Target | Timeline |
|---|---|---|---|
| C1 → C1.5 | 20 wawancara orang tua | ≥70% nyatakan frustrasi cari ide aktivitas | Minggu 1-2 |
| Fake Door Test | Landing page + waiting list | 100 signups organic | Minggu 3-4 |
| Beta (Manual) | 30 early adopters via WA/Email | Sean Ellis "Very Disappointed" ≥30% | Minggu 5-8 |
| C2 (PMF) | Full MVP launch | Sean Ellis ≥40% + D7 retention ≥50% | Minggu 8-12 |

### 2.3 Content & Quality Targets

| Metrik | Target | Metode Ukur |
|---|---|---|
| Total aktivitas di database | >1.000 published | Admin CMS |
| Kecepatan rekomendasi | <3 detik | Performance monitoring |
| Worksheet siap cetak | ≥30% dari total aktivitas | Database query |
| Assessment completion rate | >40% user aktif per bulan | Event tracking |
| Konten baru per minggu | ≥20 aktivitas baru (AI + review) | Admin CMS |
| AI content approval rate | >80% (setelah quality review) | Admin CMS |

### 2.4 Audience Acquisition Targets

| Channel | Target Volume (Bulan 6) | Biaya |
|---|---|---|
| SEO (Google organic) | 2.000 visitors/bulan | Natural |
| TikTok organic | 50.000 followers | Natural |
| Instagram organic | 10.000 followers | Natural |
| Referral (EARLYBUNDA & program) | 30% dari total signups | Rp0 per referral |
| Paid Ads (TikTok/IG) | Uji coba bulan 6-12 | Budget terpisah |

### 2.5 Brand Positioning Goal

> Menjadi aplikasi **"rekomendasi aktivitas offline untuk soft skill anak #1 di Indonesia"** — ruang aman yang tidak membuat anak kecanduan gadget, sementara orang tua tetap punya kendali lewat tracking progress & assessment.

### 2.6 North Star Metric

> **"Aktivitas offline selesai per anak per minggu"** — metrik utama yang mengukur apakah Kepompong benar-benar dipakai untuk bonding nyata, bukan sekadar dibuka lalu ditutup.

---

## 3. Struktur Paket & Pricing (Freemium Hybrid)

Berdasarkan riset pasar (benchmark kompetitor + daya beli kelas menengah Indonesia + best practice RevenueCat), Kepompong menggunakan model Freemium Hybrid — semua konten edukasi gratis (seperti Tentang Anak), monetisasi dari premium features.

### 3.1 Tier Langganan

| Layer | Harga | Fitur |
|---|---|---|
| **Gratis** | Rp0 | Rekomendasi aktivitas instan (3/detik), 3 challenge/minggu, 1 assessment dimensi/bulan, jadwal & checklist dasar |
| **Premium** | Rp39.000/bulan | Challenge unlimited + poin, assessment semua dimensi, worksheet unlimited, 2-3 profil anak, laporan mingguan |
| **Premium+** | Rp69.000/bulan | 5+ profil anak, konsultasi pakar (psikolog anak), jadwal kustom + AI suggestions, ekspor PDF report |
| **Tahunan (Premium)** | Rp390.000/tahun (~Rp32.500/bln) | Semua fitur Premium, hemat ~17% |
| **Tahunan (Premium+)** | Rp690.000/tahun (~Rp57.500/bln) | Semua fitur Premium+, hemat ~17% |

### 3.2 Diskon Launch

| Kode | Potongan | Syarat |
|---|---|---|
| `EARLYBUNDA` | Premium tahunan jadi Rp249rb (dari Rp390rb) | Waktu terbatas, kuota terbatas |
| `BUNDAGENZ` | Premium+ tahunan jadi Rp499rb (dari Rp690rb) | Khusus pengguna 1000 pertama |

### 3.3 Aturan Tambahan

- **Add-on Slot Anak:** Rp29.000/slot jika kuota profil habis (berlaku di semua tier)
- **Free Trial Premium:** 7 hari gratis untuk semua pengguna baru
- **Upgrade:** Bayar selisih harga prorata
- **Downgrade:** Kembali ke gratis setelah masa premium habis

### 3.4 Rasional Pricing

| Faktor | Detail |
|---|---|
| Daya beli kelas menengah | Rp2-9.9jt pengeluaran/bulan per kapita. Harga premium di bawah 2% dari pengeluaran bulanan |
| Benchmark | Bimi Boo Rp99rb/bln, Kidlo Rp39-85rb/bln, KidsTales Rp39-69rb/bln |
| RevenueCat 2023 | Kids app subscription punya retention 2.5x lebih tinggi dari IAP |
| Strategi | Harga Rp39rb (premium) = harga segelas bubble tea, impulse-buy friendly |

---

## 4. Fitur Utama & Alur Pengguna

### 4.1 Fitur A: Onboarding Kilat

Input form pertama kali:
- Nama Anak
- Tanggal Lahir Anak (untuk hitung usia otomatis)
- Fokus Karakter yang ingin ditingkatkan hari ini (pilih dari 5 dimensi)
- Kondisi (Indoor/Outdoor)

Setelah input → langsung ke halaman rekomendasi. Tidak ada tutorial bertele-tele.

### 4.2 Fitur B: Rekomendasi Instan 3 Detik (Core Algorithm)

Sistem mencocokkan input dengan database >1000 aktivitas, filter berdasarkan:
- Usia anak
- Agama (filter konten)
- Target soft skill
- Inddor/Outdoor
- Paket langganan (gratis vs premium)

Output: 3 kartu aktivitas konkrit + tombol "Cetak Worksheet" + tombol "Simpan ke Jadwal".

### 4.3 Fitur C: Pilar & Skill System (5 Dimensi Soft Skill & Life Skill)

Lima pilar utama pengembangan karakter:

| Pilar | Skill Contoh | Usia Mulai |
|---|---|---|
| **Karakter** | Jujur, Disiplin, Bertanggung Jawab, Religius | 2+ |
| **Sosial-Emosional** | Empati, Percaya Diri, Kerja Sama, Mengelola Emosi | 2+ |
| **Kemandirian** | Merawat Diri, Mengambil Keputusan, Inisiatif | 3+ |
| **Komunikasi** | Berbicara, Mendengarkan, Mengekspresikan Diri | 1+ |
| **Resiliensi** | Pantang Menyerah, Adaptasi, Mengatasi Masalah | 4+ |

Setiap pilar berisi 4-6 skill. Setiap skill memiliki aktivitas rekomendasi yang sesuai.

### 4.4 Fitur D: Aktivitas (11 Tipe)

| Tipe | Deskripsi |
|---|---|
| Storytelling | Cerita interaktif dengan pesan moral |
| Bermain Peran | Roleplay untuk latih empati & komunikasi |
| Permainan | Game fisik untuk latih kerjasama & aturan |
| Monolog | Aktivitas refleksi diri dan ekspresi perasaan |
| Proyek Kreatif | DIY, menggambar, membuat sesuatu |
| Musik & Gerak | Menari, bernyanyi, tepuk tangan |
| Puzzle | Teka-teki, logika, problem solving |
| Mindfulness | Latihan tenang, napas, relaksasi |
| Outdoor | Aktivitas di luar rumah |
| Ilmu Pengetahuan | Eksperimen sederhana |
| Worksheet | Lembar kerja cetak |

### 4.5 Fitur E: Challenge System (Gamified)

- Orang tua bikin challenge untuk anak (misal: "Seminggu merapikan mainan")
- Anak kumpulkan poin dari challenge yang selesai
- Poin bisa ditukar reward (ditentukan orang tua)
- Riwayat challenge tersimpan di progress
- Kategori: harian, mingguan, kustom

### 4.6 Fitur F: Assessment Soft Skill (Berkala)

- Opsional: fleksibel (2 minggu sekali atau sebulan sekali)
- Kuesioner perilaku terukur (Skala Nilai) untuk diisi orang tua
- Contoh pertanyaan per dimensi:
  - Apakah anak mengucap terima kasih tanpa diminta? (Karakter)
  - Apakah anak mau berbagi mainan dengan teman? (Sosial-Emosional)
  - Apakah anak bisa memakai baju sendiri? (Kemandirian)
  - Apakah anak bisa menceritakan kejadian hari ini? (Komunikasi)
  - Apakah anak mau mencoba lagi setelah gagal? (Resiliensi)
- Hasil: visual progress bar + grafik pertumbuhan per dimensi per bulan
- Tren positif/negatif terlihat jelas

### 4.7 Fitur G: Jadwal & Checklist Harian

- Jadwal aktivitas harian (input)
- Checklist kebiasaan (to-do list harian anak)
- Notifikasi pengingat
- Riwayat checklist tersimpan

### 4.8 Fitur H: Worksheet Cetak

- Aktivitas tertentu punya worksheet PDF yang bisa dicetak
- Satu klik download/cetak dari smartphone
- Tema: mewarnai, menulis, bercerita, eksperimen, dll
- Target: ≥30% dari total aktivitas punya worksheet

### 4.9 Fitur I: Content Management & AI Quality Control (Admin)

- Konten baru di-generate AI secara harian ke database lokal
- AI content masuk status `Pending`
- Admin review manual (`Approve` / `Reject`)
- Konten approved → status `Published`
- Target: ≥20 aktivitas baru/minggu, approval rate ≥80%

### 4.10 Fitur J: Progress Dashboard (Orang Tua)

- Progress per anak
- Grafik perkembangan per dimensi soft skill (line chart per bulan)
- Riwayat challenge selesai
- Ringkasan assessment terbaru
- Laporan mingguan via notifikasi

---

## 5. Spesifikasi Teknis & Database

### 5.1 Tech Stack

| Layer | Teknologi |
|---|---|
| **Frontend** | Svelte 5 (Vue.js alternatif) / PWA |
| **Backend** | Laravel 11+ / PHP 8.3 |
| **Database** | MariaDB / MySQL |
| **Auth** | Laravel Sanctum (Bearer token) |
| **Storage** | IndexedDB (Dexie.js) untuk offline-first |
| **Payment** | Midtrans / Xendit (QRIS, virtual account) |
| **Push Notif** | Web Push API / Firebase Cloud Messaging |

### 5.2 Entitas Database (Kunci Relasi)

| Entitas | Field Utama |
|---|---|
| `users` | id, name, email, password, phone, role, verified_at |
| `children` | id, user_id, name, birth_date, gender, agama, emoji |
| `subscriptions` | id, user_id, plan_id, starts_at, expires_at, status |
| `plans` | id, name, price, duration_days, max_children, features (json) |
| `pilars` | id, key, title, description, color, icon, ages (json), agama (json), plans (json) |
| `master_skills` | id, pilar_id, key, title, description, emoji, ages (json), agama (json), plans (json) |
| `activities` | id, type, title, description, moral, image, ages (json), skills (json), agama (json), plans (json), is_worksheet_available, status (pending/published) |
| `child_skills` | id, anak_id, skill_key, skill_title, pilar, emoji, color, progress (0-100) |
| `skill_activities` | id, child_skill_id, title, emoji, type, date, completed |
| `challenges` | id, anak_id, category, title, emoji, points, max_points, status (pending/completed/cancelled), date, meta (json) |
| `challenge_history` | id, anak_id, category, title, date, meta (json) |
| `checklists` | id, anak_id, title, items (json), date |
| `schedules` | id, anak_id, label, time, done, date |
| `worksheets` | id, anak_id, type, data (json), date |
| `evaluations` | id, anak_id, dimension, points, max_points, notes, created_at |
| `promos` | id, code, discount_percent, discount_nominal, max_uses, used, valid_until |
| `payments` | id, user_id, plan_id, amount, method, status, paid_at, expires_at |
| `notifications` | id, user_id, title, body, data (json), read_at |

### 5.3 Flow Utama

**Rekomendasi Aktivitas:**
```
User input (usia, skill target, indoor/outdoor)
    → GET /api/activities?age=X&skill=Y&location=Z
    → Filter by age, skill, agama, plan
    → Return 3 cards + worksheet availability
```

**Assessment:**
```
User buka assessment per dimensi
    → GET /api/evaluations?child_id=X&dimension=Y
    → Tampilkan kuesioner 5 pertanyaan (skala 1-5)
    → POST /api/evaluations
    → Hitung total score
    → Update progress di child_skills
    → Return grafik tren
```

**Challenge:**
```
User buat challenge
    → POST /api/challenges
    → Anak selesaikan → POST /api/challenge-history
    → Poin bertambah
    → Riwayat tersimpan
```

---

## 6. UI/UX Guidelines

### 6.1 Kriteria Keberhasilan UI

| Kriteria | Target |
|---|---|
| Kecepatan rekomendasi | <3 detik dari klik |
| Cetak worksheet | Satu klik, langsung unduh PDF |
| Bebas distraksi | Minimal animasi, target orang tua (bukan anak) |
| Onboarding | <30 detik dari buka aplikasi ke rekomendasi pertama |
| Offline mode | Semua fitur dasar tetap berjalan tanpa internet |

### 6.2 Visual Identity (dari Brand Document)

- **Vibe:** Metamorfosis alam, pertumbuhan, kehangatan keluarga
- **Palet:** Soft Leaf Green (#4CAF50), Warm Yellow/Orange (#FFB74D), Off-White (#FAFAFA), Charcoal Grey (#37474F)
- **Font:** Nunito, Quicksand, atau Poppins (rounded, ramah anak)
- **Tone:** Hangat, suportif, ilmiah, bebas distraksi

### 6.3 Bottom Navigation (Mobile)

| Tab | Ikon | Fungsi |
|---|---|---|
| Pilar (Home) | 🏠 | Pilih pilar → skill → aktivitas rekomendasi |
| Aktivitas | 🎮 | Jelajahi semua aktivitas per tipe |
| Progress | 📈 | Grafik perkembangan per anak |
| Challenge | 🏆 | Challenge aktif + buat baru |
| Profil | 👤 | Akun, billing, referral, settings |

### 6.4 Responsive Design

- **Mobile-first** (target utama: smartphone orang tua)
- **Tablet** adaptable
- **Desktop** terbatas (admin panel)
- PWA: bisa diinstal ke home screen

---

## 7. Milestone & Timeline

| Fase | Timeline | Output |
|---|---|---|
| **C1.5 — Validasi** | Minggu 1-8 | 20 wawancara, fake door test, beta manual 30 user |
| **MVP Build** | Minggu 9-16 | Landing page + auth + onboarding + rekomendasi + 500 aktivitas |
| **MVP Launch** | Minggu 17-20 | Soft launch, 1000 pengguna pertama, referral program |
| **Iterate** | Minggu 21-32 | Assessment, challenge, jadwal (dari beta) |
| **Scale** | Bulan 6-12 | Premium features, payment, target 10k registered |

# Product Requirement Document (PRD): Kepompong App

## 1. Deskripsi Produk
Kepompong adalah aplikasi berbasis langganan (SaaS) yang membantu orang tua menemukan aktivitas bermain offline dan lembar kerja (worksheet) terkurasi dalam waktu 3 detik berdasarkan usia anak dan target soft skill (seperti jujur, disiplin, mandiri) yang ingin dilatih, dilengkapi sistem asesmen perilaku berkala.

## 2. Struktur Paket & Logika Bisnis (Decoy Pricing Model)
Aplikasi menggunakan model Freemium dengan 3 tier langganan utama:
1. **Paket Coba-Coba (1 Bulan):** Rp 49.000 | Kuota Maksimal: 1 Profil Anak.
2. **Paket Fokus (3 Bulan) [Decoy]:** Rp 129.000 | Kuota Maksimal: 2 Profil Anak.
3. **Paket Juara (12 Bulan) [Hero]:** Rp 249.000 | Kuota Maksimal: 5 Profil Anak.

*   **Logika Aturan Slot Anak:** Jika pengguna berada di Paket 3 Bulan dan ingin menambah anak ke-3, sistem harus menampilkan paywall untuk Upgrade ke Paket 12 Bulan (bayar selisih) atau membeli Add-on Slot Anak seharga Rp 29.000.
*   **Kode Diskon Launching:** `EARLYBUNDA` (Potongan harga khusus paket 12 bulan dari Rp 499.000 menjadi Rp 249.000, batasi waktu & kuota penggunaan).

## 3. Fitur Utama & Alur Pengguna (User Journey)

### Fitur A: Onboarding Kilat
* Input Form pertama kali: Nama Anak, Usia Anak, Fokus Karakter yang ingin ditingkatkan hari ini, dan Kondisi (Indoor/Outdoor).

### Fitur B: Rekomendasi Instan 3 Detik (The Core Algorithm)
* Sistem mencocokkan input dengan Database berisi >1000 aktivitas yang sudah difilter berdasarkan usia, agama, paket, dan skill.
* Menampilkan 3 kartu aktivitas konkrit + 1 tombol unduh "Cetak Worksheet Paket Ini".

### Fitur C: Content Management & AI Quality Control (Internal Admin)
* Konten baru di-generate oleh AI secara harian secara otomatis ke database lokal.
* **PENTING:** Konten AI masuk ke status `Pending`. Admin harus melakukan review manual (`Approve` atau `Reject`) untuk memastikan keamanan aktivitas sebelum konten berstatus `Published` dan dapat diakses pengguna.

### Fitur D: Pelacak Soft Skill Berbasis Perilaku Nyata
* Evaluasi opsional (fleksibel: bisa 2 minggu sekali atau sebulan sekali).
* Menampilkan kuesioner perilaku terukur untuk diisi orang tua (Skala Nilai). Contoh pertanyaan:
  1. Apakah anak mengucap terima kasih tanpa diminta?
  2. Apakah anak tidak mudah mengeluh tentang makanan?
* Hasil kalkulasi kuesioner ditampilkan dalam bentuk visual progress bar/jurnal pertumbuhan anak yang hangat.

## 4. Spesifikasi Teknis & Database (Untuk Panduan AI Code Generator)

### Entitas Utama Database (Kunci Relasi):
*   `users` (id, name, email, password, subscription_tier, expires_at)
*   `children` (id, user_id, name, birth_date, gender)
*   `activities` (id, title, description, age_group, skill_category, religion_tag, status['pending', 'published'], is_worksheet_available)
*   `soft_skill_assessments` (id, child_id, point_1, point_2, point_3, point_4, point_5, total_score, created_at)
*   `promos` (id, code['EARLYBUNDA'], discount_value, max_uses, valid_until)

## 5. Kriteria Keberhasilan UI (Web & App)
* **Kecapatan Ambil Data:** Halaman rekomendasi harus termuat di bawah 3 detik.
* **Kemudahan Cetak:** Dokumen PDF Worksheet harus bisa langsung diunduh/dicetak dari smartphone orang tua dengan satu klik.
* **Bebas Distraksi Anak:** Antarmuka bersih, minim animasi berlebih untuk anak, karena target pembaca adalah orang tua.
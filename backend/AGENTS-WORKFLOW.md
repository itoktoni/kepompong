# Langkah Kecil — Workflow Documentation

## Daftar Isi

1. [Login & Register](#1-login--register)
2. [Auto-Subscribe Trial](#2-auto-subscribe-trial)
3. [Profile — Tambah Anak](#3-profile--tambah-anak)
4. [Pilar — Belajar Soft Skill](#4-pilar--belajar-soft-skill)
5. [Progress — Laporan Perkembangan](#5-progress--laporan-perkembangan)
6. [Activity — Aktivitas Harian](#6-activity--aktivitas-harian)
7. [Challenge — Tantangan](#7-challenge--tantangan)
8. [Jadwal — Jadwal Harian](#8-jadwal--jadwal-harian)
9. [Checklist — Daftar Tugas](#9-checklist--daftar-tugas)
10. [Billing — Paket & Pembayaran](#10-billing--paket--pembayaran)
11. [Referral — Affiliate & Komisi](#11-referral--affiliate--komisi)
12. [Discount — Kode Diskon](#12-discount--kode-diskon)
13. [Cashout — Pencairan Komisi](#13-cashout--pencairan-komisi)
14. [Settings](#14-settings)

---

## 1. Login & Register

### Halaman: `LoginPage.vue`

**Tampilan:**
- Toggle "Masuk" / "Daftar"
- Jika dari link referral (`?ref=XXXXXX`), mode otomatis ke "Daftar" dengan kode referral terisi

### Login

| Field | Validasi |
|-------|----------|
| Email | Wajib |
| Password | Wajib |

### Register

| Field | Validasi |
|-------|----------|
| Nama | Wajib |
| Email | Wajib, unik |
| No. Telepon | Wajib |
| Password | Wajib, min 6 karakter |
| Konfirmasi Password | Harus sama dengan password |
| Kode Referral | Opsional (auto-fill dari URL) |

### Aturan

- Tidak ada "skip login" — user wajib login atau daftar
- Setelah register, user dapat auto-subscribe trial (lihat #2)
- Jika register dengan kode referral, referrer dapat bonus (lihat #11)
- Auth token disimpan di `localStorage('lk_auth_token')`
- Tidak ada data user yang di-cache di localStorage (selalu dari API)

### API

```
POST /api/login      → { email, password }
POST /api/register   → { name, email, phone, password, password_confirmation, ref }
```

### Setelah Login

```
App.vue → onLoginSuccess()
  ├─ syncServerData() — sync data dari server ke IndexedDB
  └─ seedAndLoad()
      ├─ validateAndClearIfDifferentUser(userId) — hapus cache jika user beda
      ├─ loadAnakList() — ambil dari API
      ├─ loadToolsData() — load challenge, checklist, dll
      └─ redirect ke tab "pilar"
```

---

## 2. Auto-Subscribe Trial

### Kapan Terjadi

Saat user berhasil register (`AuthController@register`)

### Proses

```
Backend:
  ├─ User dibuat dengan role = 'trial'
  ├─ Cari plan harga=0 (Free Trial 10 Hari)
  ├─ Create Subscribe:
  │   ├─ subscribe_id_plan = plan.plan_id
  │   ├─ subsribe_value = plan.plan_value (1)
  │   ├─ subscribe_trial_at = now()
  │   ├─ subscribe_start_at = now()
  │   └─ subscribe_end_at = now() + 10 hari
  └─ user.plan = subscribe_id
```

### Data Plan

| plan_id | Nama | Value (max anak) | Periode | Harga |
|---------|------|-------------------|---------|-------|
| 1 | Free Trial 10 Hari | 1 | 10d | Rp 0 |
| 2 | Member | 1 | 1 tahun | Rp 99.000 |
| 3 | Premium | 3 | 1 tahun | Rp 159.000 |
| 4 | Family | 3 | 1 tahun | Rp 149.000 |

### Field Subscribe

| Field | Isi |
|-------|-----|
| `subscribe_trial_at` | `now()` saat registrasi |
| `subscribe_start_at` | `now()` |
| `subscribe_end_at` | `now() + plan_periode` |
| `subsribe_value` | Dari `plan.plan_value` (1 atau 3) |

---

## 3. Profile — Tambah Anak

### Halaman: `ProfileTab.vue`

**Tampilan:**
- Kartu profil (foto, nama, email, telepon, gender)
- Tombol edit profil
- Form ganti password
- Daftar anak dengan emoji, nama, usia
- Tombol "+ Tambah"
- Tombol "Logout"

### Edit Profil

| Field | Validasi |
|-------|----------|
| Nama | Wajib |
| No. Telepon | Opsional |
| Gender | Pilih "Bunda" atau "Ayah" |

### Tambah Anak — Aturan

| Role | Maks Anak | Keterangan |
|------|-----------|------------|
| `developer` | Unlimited | Tidak ada limit |
| Tidak ada plan | 0 | Popup upgrade |
| `trial` (belum expired) | `plan_value` (1) | Sesuai plan |
| `trial` (sudah expired) | 0 | Popup upgrade |
| Paid plan | `plan_value` (1-3) | Sesuai plan |

### Alur Tambah Anak

```
Klik "+ Tambah"
  ├─ developer → langsung buat
  ├─ !userPlan → showUpgradePopup
  ├─ trial? → cek expired
  │   └─ expired → showUpgradePopup
  ├─ anakList.length >= maxChildren → showUpgradePopup
  └─ OK → POST /api/langkahkecil/anak
      ├─ Backend cek: count anak di DB >= subsribe_value → 422 error
      └─ Sukses → simpan ke IndexedDB + tampilkan di list
```

### Upgrade Popup

- Muncul saat limit anak tercapai
- Tombol "Nanti Saja" → tutup popup
- Tombol "Lihat Paket" → redirect ke tab Billing

### Edit Anak

| Field | Tipe |
|-------|------|
| Nama | Text |
| Gender | Pilih "Laki-laki" / "Perempuan" |
| Tanggal Lahir | Dropdown (tanggal, bulan, tahun) |

### API

```
PUT /api/profile                      → { name, phone }
PUT /api/password                     → { old_password, password, password_confirmation }
POST /api/langkahkecil/anak           → { nama, gender, tanggal_lahir, bulan_lahir, tahun_lahir, emoji }
PUT /api/langkahkecil/anak/{id}       → { nama, gender, tanggal_lahir, bulan_lahir, tahun_lahir, emoji }
POST /api/logout
```

---

## 4. Pilar — Belajar Soft Skill

### Halaman: `PilarTab.vue`

**Tampilan:**
- Judul "Mau Belajar Apa Hari Ini?"
- Dropdown pilih anak
- 8 kartu pilar (filter berdasarkan usia anak):
  1. 🙏 Spiritual & Nilai Kehidupan (usia 2-11)
  2. 🦁 Karakter & Mental (usia 2-11)
  3. 📚 Kreatifitas & Inovasi (usia 2-11)
  4. 🧠 Disiplin & Kebiasaan Baik (usia 3-11)
  5. 🧹 Kemandirian & Life Skills (usia 3-11)
  6. 🤝 Sosial & Komunikasi (usia 2-11)
  7. ❤️ Pengelolaan Emosi & Keluarga (usia 1-11)
  8. 💪 Kesehatan & Olahraga (usia 1-11)

### Alur

```
Pilih anak → Klik pilar → Lihat skill dalam pilar
  └─ Klik skill → Skill ditambahkan ke tracking anak
      └─ Lihat aktivitas untuk skill tersebut
          └─ Klik aktivitas → Aktivitas dicatat
```

### Aturan

- Pilar difilter berdasarkan usia anak
- Skill otomatis ditambahkan ke tracking saat pertama kali dibuka
- Aktivitas otomatis dicatat saat dibuka
- Jika belum ada anak → prompt "Tambah Anak" di Profile
- Jika belum set tanggal lahir → prompt set di Profile

### API

```
POST /api/langkahkecil/anak/{id}/skills        → { key, emoji, title, pilar, color }
POST /api/langkahkecil/anak/{id}/activities     → { skill_key, title, emoji, feature }
```

---

## 5. Progress — Laporan Perkembangan

### Halaman: `ProgressTab.vue`

**Tampilan:**
- Kartu per anak yang bisa di-expand:
  - Emoji, nama, usia
  - Jumlah skill aktif & selesai
  - **Skill Aktif**: judul, pilar, progress bar, checklist aktivitas, tombol (hapus, evaluasi, share)
  - **Skill Selesai**: daftar dengan tombol hapus & share
  - **Evaluasi Selesai**: daftar evaluasi yang sudah dikerjakan

### Aksi

| Aksi | Keterangan |
|------|------------|
| Toggle aktivitas | Centang/selesai aktivitas individual |
| Hapus skill | Hapus dari tracking |
| Evaluasi | Slider penilaian 0-10 poin |
| Share | Generate kartu progress |
| Reset skill | Pindahkan dari selesai ke aktif |

### Aturan

- Evaluasi auto-save setelah 800ms tidak ada perubahan slider
- Maks poin evaluasi = 10
- Saat poin >= max_points, evaluasi ditandai "selesai"

### API

```
PUT /api/langkahkecil/anak/{id}/activities/{id}/toggle
DELETE /api/langkahkecil/anak/{id}/skills/{key}
GET /api/langkahkecil/anak/{id}/evaluations
POST /api/langkahkecil/anak/{id}/evaluations    → { skill_key, points, max_points, notes }
```

---

## 6. Activity — Aktivitas Harian

### Halaman: `ActivityTab.vue`

**Tampilan:**
- Banner sync (jumlah aktivitas di device, tombol download)
- Grid kartu aktivitas (2 kolom): emoji, judul, deskripsi, jumlah item
- 10 tipe: Story Telling, Bermain Peran, Permainan, Monolog, Proyek Kreatif, Musik & Gerak, Puzzle, Mindfulness, Outdoor, Ilmu Pengetahuan

### Aksi

- Klik kartu tipe → buka detail tipe
- Download aktivitas dari server
- Beberapa tipe punya reader khusus: Story, Roleplay, Project, Puzzle

### Aturan

- Aktivitas di-cache di IndexedDB
- Tombol download muncul "+X Baru" jika server punya lebih banyak

### API

```
GET /api/activities?grouped=1
GET /api/activities/types
```

---

## 7. Challenge — Tantangan

### Halaman: `ChallengePage.vue`

**Tampilan:**
- Daftar challenge aktif: kategori, judul, catatan, kontrol poin (-1, +1), share, hapus
- Visual "celengan" (tabungan) menunjukkan level terisi
- Riwayat: challenge selesai dikelompokkan per kategori

### Tambah/Edit Challenge

| Field | Validasi |
|-------|----------|
| Kategori | Wajib (dropdown) |
| Nama Challenge | Wajib |
| Catatan | Opsional |
| Target Poin | Wajib, min 1 |

### Aturan

- Poin: 0 ≤ points ≤ maxPoints
- Saat points == maxPoints → challenge pindah ke riwayat
- Per anak (dipilih via AnakSelector)

### API

```
POST /api/langkahkecil/anak/{id}/challenges           → { category, title, notes, maxPoints, points }
PUT /api/langkahkecil/anak/{id}/challenges/{id}        → { points } / { title, notes, maxPoints }
DELETE /api/langkahkecil/anak/{id}/challenges/{id}
POST /api/langkahkecil/anak/{id}/challenge-history     → { category, title, notes, maxPoints }
```

---

## 8. Jadwal — Jadwal Harian

### Halaman: `JadwalPage.vue`

**Tampilan:**
- Daftar jadwal: ikon, label, waktu, status selesai
- Tombol hapus per item
- Tombol "+ Tambah Jadwal"

### Tambah Jadwal

| Field | Validasi |
|-------|----------|
| Nama Aktivitas | Wajib |
| Waktu | Wajib (time picker) |

### Aturan

- **Auto-reset harian**: saat halaman dibuka, cek `localStorage('jadwal_last_reset')` vs hari ini. Jika beda, semua `done` di-reset ke false
- Per anak

### API

```
POST /api/langkahkecil/anak/{id}/schedules     → { label, time }
PUT /api/langkahkecil/anak/{id}/schedules/{id}  → { done }
DELETE /api/langkahkecil/anak/{id}/schedules/{id}
```

---

## 9. Checklist — Daftar Tugas

### Halaman: `ChecklistPage.vue`

**Tampilan:**
- Daftar checklist: judul, progress counter (X/Y), item checkbox, progress bar
- Tombol "+ Tambah Item" per checklist
- Tombol "Buat Checklist"
- Tombol share per checklist

### Aturan

- Judul checklist wajib
- Label item wajib
- Progress = item tercentang / total item
- Per anak

### API

```
POST /api/langkahkecil/anak/{id}/checklists              → { title }
PUT /api/langkahkecil/anak/{id}/checklists/{id}           → { items }
DELETE /api/langkahkecil/anak/{id}/checklists/{id}
```

---

## 10. Billing — Paket & Pembayaran

### Halaman: `BillingTab.vue`

**Tampilan:**
- **Status Card** (jika ada plan): progress bar trial (jika trial), nama plan aktif, berlaku hingga
- **Banner Pembayaran Pending** (jika ada): kode order, jumlah, tombol batal
- **Daftar Plan**: kartu expandable per plan
- **Riwayat Pembayaran**: daftar dengan status (paid/pending/expired)

### Pilih Plan

| Status | Tombol | Keterangan |
|--------|--------|------------|
| Downgrade | Disabled | Tidak bisa pilih plan lebih murah |
| Plan saat ini | "Perpanjang" | Perpanjang langganan |
| Plan lain | "Pilih Paket Ini" | Buka checkout |
| Plan gratis + aktif | "Paket aktif saat ini" | Disabled |

### Checkout Flow

```
Klik "Pilih Paket Ini"
  ├─ Buka modal checkout
  ├─ Input kode diskon (opsional)
  │   └─ Klik "Pakai" → validasi via API
  │       ├─ Valid → tampilkan diskon & total
  │       └─ Invalid → tampilkan error
  └─ Klik "Bayar"
      ├─ POST /api/payments → buat payment + QRIS
      ├─ Tampilkan QR code
      ├─ Polling status setiap 3 detik
      │   ├─ Paid → sukses, refresh user data
      │   ├─ Expired → tampilkan error
      │   └─ Cancelled → tampilkan error
      └─ "Nanti Saja" → tutup (payment tetap pending)
```

### Aturan

- **Maks 10 payment per hari** per user (backend, return 429)
- Payment expired setelah 30 menit
- Payment pending otomatis di-cancel saat buat payment baru
- Downgrade tidak diizinkan (harga plan < harga plan saat ini)
- Diskon divalidasi: status aktif, rentang tanggal, min transaksi, max amount

### API

```
GET /api/langkahkecil/plans
POST /api/payments/validate-discount    → { code, plan_id }
POST /api/payments                      → { plan_id, discount_code }
GET /api/payments/{id}
POST /api/payments/{id}/cancel
GET /api/payments
```

---

## 11. Referral — Affiliate & Komisi

### Halaman: `ReferralTab.vue`

**Tampilan:**
- **Kartu Hero**: kode referral, link, tombol copy/share, tombol edit data
- **3 tab**: Affiliate, Diskon, Pencairan

### Tab: Affiliate

- Statistik: total referral, bonus per daftar (Rp 500), total diperoleh
- Daftar referral: nama, email (disamarkan), role badge, tanggal gabung

### Aturan Affiliate

- Referral daftar via `?ref=XXXXXX` → referrer dapat bonus register (Rp 500)
- Saat referral upgrade → referrer dapat komisi dari harga plan
- Komisi dihitung real-time dari tabel `affiliate` (bukan field di user)

```
komisi = SUM(affiliate_jumlah WHERE status != 'rejected')
       - SUM(cashout_jumlah + cashout_admin_fee WHERE status IN ('pending','completed'))
```

### Komisi per Upgrade

```
Komisi = plan_harga × commission_rate / 100

Contoh:
  Plan Premium Rp159.000, commission rate 15%
  Komisi = 159.000 × 15% = Rp23.850

Dengan discount code 2%:
  Effective rate = 15% - 2% = 13%
  Komisi = 159.000 × 13% = Rp20.670
```

### Affiliate Switch

Ketika customer pakai discount code dari affiliate B (berbeda dari referrer A):
- Komisi masuk ke B (bukan A)
- `affiliate_reff` customer pindah ke B
- Transaksi lama tetap di A

### API

```
GET /api/referrals
PUT /api/affiliate-code    → { affiliate_code }
POST /api/rekening         → { rekening_nama, rekening_bank, rekening_nomor }
```

---

## 12. Discount — Kode Diskon

### Halaman: `ReferralTab.vue` → Tab Diskon

**Tampilan:**
- Form buat kode diskon: kode, nama, nilai (%)
- Daftar kode diskon: kode, nama, nilai, tombol copy, tombol hapus

### Buat Kode Diskon

| Field | Validasi |
|-------|----------|
| Kode | Wajib, 4-20 karakter, uppercase, unik |
| Nama | Wajib, max 100 karakter |
| Tipe | "percentage" atau "fixed" |
| Nilai | Wajib, min 1 |

### Aturan

| Config | Default | Keterangan |
|--------|---------|------------|
| `max_discounts` | 3 | Maks kode per affiliate |
| `max_discount_value` | 15% | Maks persentase |
| `max_discount_nominal` | Rp10.000 | Maks nominal fixed |

### Tracking

Semua discount ter-track di tabel `discounts`:
- `discount_created_by` → siapa yang buat (via HasUserstamps)
- Saat customer pakai kode → `payment_diskon_code` tersimpan di payment
- Saat payment settle → komisi affiliate dihitung berdasarkan discount value

### API

```
GET /api/discounts
POST /api/discounts       → { discount_code, discount_nama, discount_type, discount_value }
DELETE /api/discounts/{id}
```

---

## 13. Cashout — Pencairan Komisi

### Halaman: `ReferralTab.vue` → Tab Pencairan

**Tampilan:**
- Statistik: sudah dicairkan, pending, saldo tersedia
- Tombol "Cairkan" → modal dengan slider

### Cashout Modal

- Slider: min Rp50.000, max = saldo tersedia
- Tampilkan: platform fee (3%), total potongan, jumlah diterima
- Tombol cepat: Min, 25%, 50%, Max

### Aturan

| Config | Default | Keterangan |
|--------|---------|------------|
| `cashout.minimum` | Rp50.000 | Minimal pencairan |
| `cashout.admin_rate` | 3% | Biaya platform |

### Alur

```
Klik "Cairkan"
  ├─ Set jumlah via slider
  ├─ Validasi:
  │   ├─ komisi() >= jumlah + admin_fee
  │   ├─ rekening_nama, rekening_bank, rekening_nomor ada
  │   └─ jumlah >= minimum
  ├─ Create Cashout record (status: pending)
  └─ Admin proses → status berubah ke 'completed' atau 'rejected'
```

### Contoh Perhitungan

```
Saldo tersedia: Rp100.000
Jumlah dicairkan: Rp50.000
Admin fee (3%): Rp1.500
Total potongan: Rp51.500
Diterima: Rp50.000
```

### API

```
POST /api/cashout     → { amount }
GET /api/cashouts
```

---

## 14. Settings

### Halaman: `SettingsTab.vue`

**Tampilan:**
- Pengaturan sync (toggle auto-sync)
- Pengaturan notifikasi (subscribe/unsubscribe push)
- Download aktivitas dari server
- Info app (nama, tagline, versi)

### Aturan

- Download aktivitas butuh login
- Aktivitas di-cache di IndexedDB
- Push notification pakai VAPID keys

### API

```
GET /api/push/vapid-key
POST /api/push/subscribe
POST /api/push/unsubscribe
GET /api/push/status
GET /api/activities?grouped=1
```

---

## Cross-Cutting: Multi-User Protection

### Masalah

User A login dengan anak 1,2,3. User B login di device yang sama → data anak A muncul di B.

### Solusi

```
App.vue → seedAndLoad()
  └─ validateAndClearIfDifferentUser(userId)
      ├─ Cek lk_cache_user_id di localStorage
      ├─ Beda? → clearAllUserData()
      │   ├─ Hapus semua tabel IndexedDB (anak, challenges, checklists, schedules, worksheets)
      │   └─ Hapus lk_anak_cache di localStorage
      └─ Set lk_cache_user_id = userId
```

### Data yang Dilindungi

| Data | Storage | Mekanisme |
|------|---------|-----------|
| Anak list | IndexedDB `anak` + `lk_anak_cache` | Clear saat user beda |
| Challenges | IndexedDB `challenges` | Clear saat user beda |
| Challenge History | IndexedDB `challengeHistory` | Clear saat user beda |
| Checklists | IndexedDB `checklists` | Clear saat user beda |
| Schedules | IndexedDB `schedules` | Clear saat user beda |
| Worksheets | IndexedDB `worksheets` | Clear saat user beda |

### Data yang Selalu dari API

| Data | Endpoint |
|------|----------|
| User profile | `/me` |
| Plans | `/plans` |
| Discounts | `/discounts` |
| Affiliate/Earnings | `/referrals` |
| Cashouts | `/cashouts` |
| Payments | `/payments` |

---
title: "Cara Upload Aktivitas Sendiri"
description: "Pelajari cara membuat dan upload aktivitas Kepompongmu sendiri dalam format ZIP. Contoh lengkap untuk semua tipe aktivitas."
category: "Panduan"
level: "menengah"
duration: "15 menit"
draft: false
---

# Cara Upload Aktivitas Sendiri

Buat aktivitas Kepompongmu sendiri dan upload ke aplikasi! Ikuti panduan langkah demi langkah berikut.

## Langkah-langkah Upload

### 1. Siapkan File `data.json`

Buat file bernama `data.json` berisi metadata aktivitas. File ini wajib ada di dalam ZIP.

### 2. Siapkan Gambar

- `cover.png` — gambar sampul (opsional, jika tidak ada maka gambar pertama jadi cover)
- `1.png` sampai `8.png` — gambar halaman
- Format: **PNG, JPG, atau WEBP**, maksimal 10MB per gambar

### 3. Buat File ZIP

Kompres `data.json` + semua gambar ke dalam satu file ZIP. **Jangan pakai subfolder!**

```
aktivitas-saya.zip
├── data.json
├── cover.png
├── 1.png
├── 2.png
├── 3.png
├── 4.png
├── 5.png
├── 6.png
├── 7.png
└── 8.png
```

### 4. Upload di Aplikasi

1. Buka aplikasi Kepompong
2. Masuk ke halaman **Generate Idea**
3. Di sidebar, pilih file ZIP di kolom **"Upload ZIP"**
4. Klik tombol **📦 ZIP**
5. Tunggu hingga upload selesai
6. Status aktivitas akan menjadi **"pending"** untuk review

## Jumlah Halaman yang Valid

| Total | Halaman Cerita | Grid |
|-------|---------------|------|
| 4 | 3 | 2×2 |
| 9 | 8 | 3×3 |
| 16 | 15 | 4×4 |
| 25 | 24 | 5×5 |

---

## Contoh per Tipe Aktivitas

Di bawah ini contoh `data.json` untuk setiap tipe. Klik **Download ZIP** untuk download contoh lengkap (data.json + gambar).

---

### 📖 Storytelling — Cerita Bergambar

Field: `pages[]` berisi teks cerita per halaman.

**[📦 Download Contoh ZIP](/examples/storytelling-example.zip)**

```json
{
  "type": "storytelling",
  "title": "Kelinci dan Kura-Kura",
  "desc": "Cerita tentang kelinci yang sombong dan kura-kura yang tekun.",
  "moral": "Kita harus tekun dan tidak sombong.",
  "emoji": "📖",
  "pages": [
    { "text": "Di sebuah hutan, hidup seekor kelinci yang sangat cepat. Ia selalu sombong." },
    { "text": "Suatu hari, kelinci menantang kura-kura untuk berlomba." },
    { "text": "Kura-kura menerima tantangan itu dengan tenang." },
    { "text": "Perlomba pun dimulai! Kelinci berlari sangat cepat." },
    { "text": "Karena merasa sudah jauh di depan, kelinci memutuskan untuk tidur." },
    { "text": "Sementara itu, kura-kura terus berjalan perlahan tapi pasti." },
    { "text": "Ketika kelinci terbangun, kura-kura sudah hampir sampai finish!" },
    { "text": "Kura-kura memenangkan perlombaan. Kelinci pun belajar tidak sombong." }
  ]
}
```

---

### 🎭 Bermain Peran — Drama Anak

Field: `pages[]` + `roles[]` (daftar karakter).

**[📦 Download Contoh ZIP](/examples/bermain_peran-example.zip)**

```json
{
  "type": "bermain_peran",
  "title": "Dokter Hewan",
  "desc": "Bermain peran menjadi dokter hewan yang merawat hewan sakit.",
  "moral": "Bermain peran membantu kita memahami perasaan orang lain.",
  "emoji": "🎭",
  "roles": [
    { "name": "Dokter Hewan", "desc": "Memeriksa dan merawat hewan" },
    { "name": "Perawat", "desc": "Membantu dokter menyiapkan alat" },
    { "name": "Pemilik Hewan", "desc": "Membawa hewan ke dokter" }
  ],
  "pages": [
    { "text": "Selamat datang di klinik hewan! Hari ini kita bermain peran." },
    { "text": "Dokter hewan mengenakan jas putih dan menyiapkan alat-alat." },
    { "text": "Pemilik hewan datang membawa kucing yang sakit." },
    { "text": "Dokter memeriksa kucing dengan lembut." },
    { "text": "Kucing demam, kata dokter. Perawat menyiapkan obat." },
    { "text": "Setelah minum obat, kucing terlihat lebih baik." },
    { "text": "Datang lagi seekor anjing yang kakinya terluka." },
    { "text": "Kerja bagus, tim! Semua hewan sudah sehat kembali." }
  ]
}
```

---

### 🎲 Permainan — Game Seru

Field: `pages[]` berisi instruksi permainan.

**[📦 Download Contoh ZIP](/examples/permainan-example.zip)**

```json
{
  "type": "permainan",
  "title": "Petak Umpet",
  "desc": "Permainan petak umpet yang seru dan menyenangkan.",
  "moral": "Bermain dengan aturan mengajarkan kita sportivitas.",
  "emoji": "🎲",
  "pages": [
    { "text": "Ayo bermain petak umpet! Permainan ini sangat seru!" },
    { "text": "Pertama, tentukan siapa yang jaga. Gunakan hompimpa!" },
    { "text": "Yang jaga menutup mata dan menghitung sampai sepuluh." },
    { "text": "Yang lain bersembunyi di tempat yang aman." },
    { "text": "Yang jaga mencari teman-teman yang bersembunyi." },
    { "text": "Kalau ketemu, kejar dan sentuh sebelum dia pulang!" },
    { "text": "Kalau berhasil pulang, dia aman. Kalau tertangkap, dia jaga." },
    { "text": "Seru ya! Ayo main lagi!" }
  ]
}
```

---

### 🎤 Monolog — Pidato Anak

Field: `script` (teks pidato) + `tips[]` (tips penyampaian). Tidak perlu gambar halaman.

**[📦 Download Contoh ZIP](/examples/monolog-example.zip)**

```json
{
  "type": "monolog",
  "title": "Pagi yang Ceria",
  "desc": "Monolog tentang rutinitas pagi hari yang menyenangkan.",
  "moral": "Berani berbicara di depan orang lain adalah keberanian yang hebat.",
  "emoji": "🎤",
  "script": "Halo semuanya! Namaku akan menceritakan pagi hari yang ceria. Setiap pagi aku bangun dengan senyuman. Pertama, aku meregangkan badan. Lalu aku pergi ke kamar mandi. Ibu sudah menyiapkan sarapan yang enak. Aku makan dengan lahap. Setelah sarapan, aku memakai baju seragam. Aku mencium tangan ibu dan ayah. Pagi yang ceria membuat hari jadi menyenangkan!",
  "tips": [
    "Ucapkan dengan suara yang jelas dan lantang",
    "Gunakan ekspresi wajah yang ceria",
    "Gerakkan tangan untuk membantu menceritakan",
    "Buat jeda sebentar di antara setiap kegiatan",
    "Tunjukkan antusiasme saat menceritakan",
    "Tersenyumlah saat mengucapkan salam",
    "Gunakan nada suara yang berbeda untuk dialog",
    "Akhiri dengan senyuman yang lebar"
  ]
}
```

---

### 🎨 Proyek Kreatif — Kerajinan Tangan

Field: `pages[]` + `materials[]` (bahan) + `steps[]` (langkah).

**[📦 Download Contoh ZIP](/examples/proyek_kreatif-example.zip)**

```json
{
  "type": "proyek_kreatif",
  "title": "Membuat Kincir Angin",
  "desc": "Membuat kincir angin dari kertas bekas.",
  "moral": "Berkarya dengan tangan melatih kreativitas dan kesabaran.",
  "emoji": "🎨",
  "materials": ["Kertas warna", "Gunting", "Pensil", "Lem", "Tusuk sate"],
  "steps": [
    { "text": "Siapkan kertas berwarna berbentuk persegi." },
    { "text": "Gunting dari setiap pojok ke tengah." },
    { "text": "Lipat setiap ujung ke tengah dan lem." },
    { "text": "Tusuk bagian tengah dengan tusuk sate." },
    { "text": "Kincir angin siap diputar!" }
  ],
  "pages": [
    { "text": "Ayo membuat kincir angin dari kertas!" },
    { "text": "Siapkan bahan-bahannya dulu ya." },
    { "text": "Gunting kertas dari pojok ke tengah." },
    { "text": "Lipat ujungnya ke tengah dan lem." },
    { "text": "Tusuk bagian tengah dengan tusuk sate." },
    { "text": "Hias kincir angin sesukamu!" },
    { "text": "Tiup kincir anginnya. Berputar!" },
    { "text": "Kincir angin cantik sudah jadi!" }
  ]
}
```

---

### 🎵 Musik & Gerak — Lagu Anak

Field: `lyrics` (lirik lagu) + `moves[]` (gerakan).

**[📦 Download Contoh ZIP](/examples/musik_gerak-example.zip)**

```json
{
  "type": "musik_gerak",
  "title": "Lompat Katak",
  "desc": "Lagu dan gerakan lompat katak yang seru.",
  "moral": "Musik dan gerak membuat tubuh sehat dan hati senang.",
  "emoji": "🎵",
  "lyrics": "Lompat katak, lompat katak, lompat ke sana sini. Lompat katak, lompat katak, badan jadi sehat sendiri.",
  "moves": [
    { "text": "Jongkok seperti katak, lalu lompat ke depan." },
    { "text": "Lompat ke kiri dan ke kanan mengikuti irama." },
    { "text": "Angkat tangan ke atas saat melompat." },
    { "text": "Tepuk tangan dua kali setelah mendarat." },
    { "text": "Putar badan setengah lingkaran." },
    { "text": "Jongkok lagi dan lompat setinggi mungkin." },
    { "text": "Ayunkan tangan ke depan dan belakang." },
    { "text": "Akhiri dengan membungkuk sambil tersenyum." }
  ]
}
```

---

### 🧩 Puzzle — Teka-Teki

Field: `questions[]` berisi soal + jawaban + petunjuk.

**[📦 Download Contoh ZIP](/examples/puzzle-example.zip)**

```json
{
  "type": "puzzle",
  "title": "Tebak Binatang",
  "desc": "Puzzle tebak binatang dari petunjuk yang diberikan.",
  "moral": "Bermain puzzle melatih otak kita berpikir dengan cerdas.",
  "emoji": "🧩",
  "questions": [
    {
      "question": "Aku punya belalai yang panjang. Aku binatang terbesar di darat. Siapakah aku?",
      "answer": "Gajah",
      "hint": "Aku suka mandi dengan menyemprotkan air pakai belalai."
    },
    {
      "question": "Aku punya leher yang sangat panjang. Aku bisa makan daun di pohon tinggi.",
      "answer": "Jerapah",
      "hint": "Aku adalah binatang tertinggi di dunia."
    },
    {
      "question": "Aku hidup di air dan punya sisik yang indah.",
      "answer": "Ikan",
      "hint": "Aku bernapas dengan insang."
    },
    {
      "question": "Aku bisa terbang dan punya bulu yang cantik.",
      "answer": "Burung",
      "hint": "Aku punya sayap dan paruh."
    },
    {
      "question": "Aku punya cangkang keras di punggungku. Aku berjalan sangat lambat.",
      "answer": "Kura-kura",
      "hint": "Aku bisa masuk ke dalam cangkang kalau takut."
    },
    {
      "question": "Aku suka makan wortel dan punya telinga yang panjang.",
      "answer": "Kelinci",
      "hint": "Aku punya ekor yang kecil dan bulat."
    },
    {
      "question": "Aku punya belang hitam dan putih. Aku hidup di hutan bambu.",
      "answer": "Panda",
      "hint": "Aku suka makan bambu setiap hari."
    },
    {
      "question": "Aku binatang yang suka menggonggong. Aku sahabat manusia.",
      "answer": "Anjing",
      "hint": "Aku suka mengibaskan ekor kalau senang."
    }
  ]
}
```

---

### 🧘 Mindfulness — Latihan Pernapasan

Field: `pages[]` + `steps[]` (langkah latihan) + `benefit` (manfaat).

**[📦 Download Contoh ZIP](/examples/mindfulness-example.zip)**

```json
{
  "type": "mindfulness",
  "title": "Bernapas Dalam",
  "desc": "Latihan pernapasan dalam untuk menenangkan pikiran.",
  "moral": "Menenangkan pikiran membantu kita merasa lebih baik.",
  "emoji": "🧘",
  "benefit": "Membantu menenangkan pikiran, mengurangi cemas, dan membuat tubuh rileks.",
  "steps": [
    { "text": "Duduk dengan nyaman. Punggung tegak tapi rileks." },
    { "text": "Tutup mata perlahan. Rasakan tubuhmu." },
    { "text": "Tarik napas dalam-dalam lewat hidung. Hitung sampai empat." },
    { "text": "Tahan napas sebentar. Hitung sampai tiga." },
    { "text": "Hembuskan napas pelan-pelan lewat mulut. Hitung sampai enam." },
    { "text": "Ulangi tiga kali. Setiap kali lebih rileks." },
    { "text": "Bernapas biasa sekarang. Rasakan ketenangan." },
    { "text": "Buka mata perlahan. Senyum! Tubuh terasa segar." }
  ],
  "pages": [
    { "text": "Ayo latihan bernapas dalam! Ini akan membantu kita tenang." },
    { "text": "Duduk dengan nyaman. Punggung tegak, tangan di lutut." },
    { "text": "Tarik napas dalam-dalam lewat hidung." },
    { "text": "Tahan napas sebentar. Bayangkan udara tenang." },
    { "text": "Hembuskan napas pelan-pelan lewat mulut." },
    { "text": "Ulangi tiga kali. Tarik, tahan, hembuskan." },
    { "text": "Bernapas biasa. Rasakan dada naik turun." },
    { "text": "Buka mata pelan-pelan. Senyum! Tubuh segar." }
  ]
}
```

---

### 🌿 Outdoor — Eksplorasi Alam

Field: `pages[]` + `steps[]` (langkah kegiatan) + `observation` (apa yang diamati).

**[📦 Download Contoh ZIP](/examples/outdoor-example.zip)**

```json
{
  "type": "outdoor",
  "title": "Mengamati Awan",
  "desc": "Mengamati bentuk awan di langit.",
  "moral": "Mengenal alam mengajarkan kita mencintai lingkungan.",
  "emoji": "🌿",
  "steps": [
    { "text": "Keluar rumah dan cari tempat yang nyaman." },
    { "text": "Duduk atau berbaring di rumput." },
    { "text": "Lihat ke langit. Perhatikan awan di atas." },
    { "text": "Coba tebak bentuk awan. Mirip apa?" },
    { "text": "Amati pergerakan awan. Apakah berubah bentuk?" },
    { "text": "Ceritakan kepada teman apa yang kamu lihat." },
    { "text": "Gambarkan bentuk awan di buku gambar." },
    { "text": "Simpan gambarmu sebagai kenang-kenangan!" }
  ],
  "observation": "Ceritakan bentuk awan yang kamu lihat! Apakah mirip hewan, benda, atau tokoh kartun?",
  "pages": [
    { "text": "Ayo keluar rumah dan mengamati awan di langit!" },
    { "text": "Duduk di rumput dan lihat ke atas." },
    { "text": "Coba tebak bentuk awan. Mirip apa ya?" },
    { "text": "Ada awan yang mirip kelinci! Lihat!" },
    { "text": "Awan bergerak pelan-pelan. Bentuknya berubah." },
    { "text": "Sekarang mirip kapal laut! Lucu ya!" },
    { "text": "Gambarkan awan yang kamu lihat." },
    { "text": "Alam itu indah ya! Ayo jaga lingkungan." }
  ]
}
```

---

### 🔬 Ilmu Pengetahuan — Eksperimen Sains

Field: `pages[]` + `materials[]` (bahan) + `steps[]` (langkah) + `explanation` (penjelasan).

**[📦 Download Contoh ZIP](/examples/ilmu_pengetahuan-example.zip)**

```json
{
  "type": "ilmu_pengetahuan",
  "title": "Pelangi",
  "desc": "Belajar tentang pelangi dan warnanya.",
  "moral": "Belajar sains membuka pikiran kita tentang dunia.",
  "emoji": "🔬",
  "materials": ["Gelas bening", "Air", "Senter", "Kertas putih"],
  "steps": [
    { "text": "Isi gelas bening dengan air sampai penuh." },
    { "text": "Letakkan gelas di dekat jendela yang ada sinar matahari." },
    { "text": "Letakkan kertas putih di belakang gelas." },
    { "text": "Amati kertas putih. Ada warna pelangi!" },
    { "text": "Hitung warnanya: merah, jingga, kuning, hijau, biru, nila, ungu." }
  ],
  "explanation": "Cahaya matahari terdiri dari banyak warna. Ketika melewati air, cahaya terpecah menjadi warna-warna pelangi. Ini disebut pembiasan cahaya.",
  "pages": [
    { "text": "Ayo belajar tentang pelangi! Tahukah kamu warna pelangi?" },
    { "text": "Siapkan gelas bening dan isi dengan air." },
    { "text": "Letakkan gelas di dekat jendela yang ada sinar matahari." },
    { "text": "Letakkan kertas putih di belakang gelas." },
    { "text": "Amati kertasnya! Ada warna pelangi!" },
    { "text": "Warnanya: merah, jingga, kuning, hijau, biru, nila, ungu." },
    { "text": "Cahaya matahari terpecah karena pembiasan di air." },
    { "text": "Seru ya! Ayo coba lagi di rumah!" }
  ]
}
```

---

### 🤔 Tebak-tebakan — Teka-Teki Lucu

Field: `questions[]` berisi pertanyaan + jawaban + petunjuk.

**[📦 Download Contoh ZIP](/examples/tebak_tebakan-example.zip)**

```json
{
  "type": "tebak_tebakan",
  "title": "Tebak Buah",
  "desc": "Teka-teki tentang buah-buahan.",
  "moral": "Bermain teka-teki melatih daya pikir dan kreativitas.",
  "emoji": "🤔",
  "questions": [
    {
      "question": "Aku kuning dan panjang. Kera sangat suka aku. Buah apa aku?",
      "answer": "Pisang",
      "hint": "Aku enak dimakan langsung atau dibuat gorengan."
    },
    {
      "question": "Aku merah dan bulat. Anak-anak suka jus aku. Buah apa aku?",
      "answer": "Apel",
      "hint": "Kata pepatah, makan aku setiap hari bikin sehat."
    },
    {
      "question": "Aku hijau di luar, merah di dalam, banyak bijinya. Buah apa aku?",
      "answer": "Semangka",
      "hint": "Aku sangat segar dimakan saat cuaca panas."
    },
    {
      "question": "Aku kecil-kecil dan ungu. Rasanya manis dan asam. Buah apa aku?",
      "answer": "Anggur",
      "hint": "Aku sering dijadikan jus atau dimakan langsung."
    },
    {
      "question": "Aku berduri di luar, kuning di dalam. Baunya sangat khas. Buah apa aku?",
      "answer": "Durian",
      "hint": "Aku disebut raja buah di Asia Tenggara."
    },
    {
      "question": "Aku oranye dan panjang. Kelinci sangat suka aku. Sayur apa aku?",
      "answer": "Wortel",
      "hint": "Aku baik untuk kesehatan mata."
    },
    {
      "question": "Aku hijau dan bulat kecil. Sering jadi campuran salad. Apa aku?",
      "answer": "Anggur hijau",
      "hint": "Rasanya asam segar."
    },
    {
      "question": "Aku kuning-oranye dan berbulu. Rasanya manis legit. Buah apa aku?",
      "answer": "Nanas",
      "hint": "Aku punya mahkota di atas kepala."
    }
  ]
}
```

---

### 🤲 Permainan Tangan — Permainan Jari

Field: `how` (cara bermain) + `rules[]` (aturan) + `moves[]` (gerakan) + `lyrics` (nyanyian).

**[📦 Download Contoh ZIP](/examples/permainan_tangan-example.zip)**

```json
{
  "type": "permainan_tangan",
  "title": "Cubit-Cubit",
  "desc": "Permainan tangan yang seru untuk anak.",
  "moral": "Permainan tangan melatih koordinasi dan kerja sama.",
  "emoji": "🤲",
  "how": "Duduk berhadapan dengan teman. Ikuti gerakan sambil bernyanyi.",
  "rules": [
    "Duduk berhadapan dengan teman",
    "Ikuti gerakan sesuai nyanyian",
    "Jangan terlalu keras mencubit",
    "Tertawa dan bersenang-senang!"
  ]
}
```

---

### 🧠 Latihan Otak — Latihan Kognitif

Field: `exercises[]` berisi soal-soal latihan.

**[📦 Download Contoh ZIP](/examples/latihan_otak-example.zip)**

```json
{
  "type": "latihan_otak",
  "title": "Menghitung dan Mencocokkan",
  "desc": "Latihan menghitung dan mencocokkan untuk anak.",
  "moral": "Melatih otak setiap hari membuat kita semakin pintar.",
  "emoji": "🧠",
  "exercises": [
    { "text": "Berapa jumlah jari di satu tangan? Hitung ya!" },
    { "text": "Mana yang lebih besar: gajah atau semut?" },
    { "text": "Warna apa yang terbuat dari campuran merah dan kuning?" },
    { "text": "Berapa sisi yang dimiliki segitiga?" },
    { "text": "Mana yang lebih cepat: kura-kura atau kelinci?" },
    { "text": "Hewan apa yang bisa terbang?" },
    { "text": "Berapa jumlah hari dalam seminggu?" },
    { "text": "Buah apa yang warnanya sama dengan matahari?" }
  ]
}
```

---

### 💬 Komik — Cerita Bergambar

Field: `pages[]` berisi dialog narasi per panel.

**[📦 Download Contoh ZIP](/examples/komik-example.zip)**

```json
{
  "type": "komik",
  "title": "Petualangan di Kebun",
  "desc": "Komik petualangan anak-anak yang belajar berkebun.",
  "moral": "Membaca komik mengajarkan kita hal baru dengan cara yang seru.",
  "emoji": "💬",
  "pages": [
    { "text": "Pagi hari yang cerah. Anak-anak pergi ke kebun belakang rumah." },
    { "text": "Pertama, mereka menyiapkan tanah. Tanahnya gembur dan bagus!" },
    { "text": "Lalu mereka menanam benih sayuran. Ada wortel, tomat, dan cabai." },
    { "text": "Setelah menanam, mereka menyiram tanaman. Cuk-cuk-cuk!" },
    { "text": "Beberapa hari kemudian, muncul tunas kecil dari tanah." },
    { "text": "Mereka rajin menyiram setiap pagi dan sore." },
    { "text": "Setelah beberapa minggu, sayuran sudah besar!" },
    { "text": "Akhirnya mereka memanen. Ibu memasak jadi makanan lezat!" }
  ]
}
```

---

### 🖍️ Coloring — Mewarnai Gambar

Field: `slides[]` (instruksi mewarnai per gambar) + `tags[]` (tag).

**[📦 Download Contoh ZIP](/examples/coloring-example.zip)**

```json
{
  "type": "coloring",
  "title": "Hewan Laut",
  "desc": "Mewarnai gambar hewan-hewan laut yang lucu.",
  "moral": "Mewarnai melatih kreativitas dan kesabaran.",
  "emoji": "🖍️",
  "slides": [
    { "text": "Ikan badut hidup di antara anemon laut. Warnai dengan warna jingga dan putih!" },
    { "text": "Kura-kura laut berenang di laut dalam. Warnai dengan hijau dan cokelat!" },
    { "text": "Gurita punya delapan tentakel. Warnai dengan merah atau ungu!" },
    { "text": "Paus adalah mamalia terbesar di laut. Warnai dengan biru tua!" },
    { "text": "Bintang laut hidup di dasar laut. Warnai dengan merah atau pink!" },
    { "text": "Kuda laut berenang tegak. Warnai dengan kuning atau cokelat!" },
    { "text": "Ubur-ubur bisa bercahaya. Warnai dengan biru muda atau pink!" },
    { "text": "Lumba-lumba suka melompat. Warnai dengan abu-abu dan biru!" }
  ],
  "tags": ["ikan", "laut", "mewarnai", "hewan", "kura-kura", "gurita", "paus", "lumba-lumba"]
}
```

---

### 🪣 Mengenal Kata — Belajar Membaca

Field: `slides[]` (kata + penjelasan per slide) + `tags[]` (tag).

**[📦 Download Contoh ZIP](/examples/mengenal_kata-example.zip)**

```json
{
  "type": "mengenal_kata",
  "title": "Buah-Buahan",
  "desc": "Mengenal nama buah-buahan dan bentuknya.",
  "moral": "Mengenal kata baru membantu kita belajar membaca.",
  "emoji": "🔤",
  "slides": [
    { "text": "APEL — Apel berwarna merah atau hijau. Rasanya manis dan renyah." },
    { "text": "PISANG — Pisang berwarna kuning. Rasanya manis dan lembut." },
    { "text": "JERUK — Jeruk berwarna oranye. Rasanya manis asam dan segar." },
    { "text": "ANGGUR — Anggur kecil-kecil dan berwarna ungu. Manis banget!" },
    { "text": "SEMANGKA — Semangka besar dan hijau. Dalamnya merah dan segar." },
    { "text": "MANGGA — Mangga kuning oranye. Wangi dan manis!" },
    { "text": "STROBERI — Stroberi kecil merah. Rasanya asam manis." },
    { "text": "NANAS — Nanas kuning dan berduri. Manis dan sedikit asam." }
  ],
  "tags": ["buah", "makanan", "belajar", "kata", "membaca"]
}
```

---

## Tips Membuat Aktivitas

- Gunakan judul yang singkat dan menarik
- Tulis teks yang mudah dipahami anak usia 1-10 tahun
- Gunakan bahasa Indonesia yang sederhana
- Gambar sebaiknya berwarna cerah dan menarik
- Jangan gunakan kata-kata sulit atau bahasa asing
- Jangan gunakan "si" di judul (misal: "Kelinci" bukan "Si Kelinci")
- Jangan gunakan nama karakter atau tempat di judul

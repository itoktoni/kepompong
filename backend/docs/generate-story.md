# generate:story

Command Laravel untuk generate cerita anak dengan AI dan langsung simpan ke database.

## Penggunaan Dasar

```bash
php artisan generate:story <theme>
```

## Argument

| Argument | Wajib | Deskripsi |
|----------|-------|-----------|
| `theme` | Ya | Tema cerita (contoh: `kebersamaan`, `kejujuran`, `kisah_nabi`) |

## Options

| Option | Default | Deskripsi |
|--------|---------|-----------|
| `--child=` | `-` | Nama anak dalam cerita. Jika kosong, AI buat karakter sendiri |
| `--pages=` | `16` | Jumlah halaman (termasuk cover) |
| `--ages=` | `3,4,5,6,7,8` | Target umur. Angka tunggal (7 = `[6,7,8,9,10]`) atau comma-separated (`3,4,5,6`) |
| `--agama=` | `-` | Tag agama: `islam`, `kristen`, `katholik`, `hindu`, `budha` |

## Contoh

### Generate cerita biasa
```bash
php artisan generate:story kebersamaan
```

### Dengan nama anak
```bash
php artisan generate:story kejujuran --child=Budi
```

### Cerita Islami
```bash
php artisan generate:story kisah_nabi --agama=islam --pages=5
```

### Target umur spesifik
```bash
php artisan generate:story persahabatan --ages=7 --agama=kristen
```

### Full options
```bash
php artisan generate:story kemandirian --child=Rina --pages=3 --ages=5,6,7,8 --agama=islam
```

## Behavior Otomatis

### Nama Anak (`--child`)
Jika tidak diisi, nama di-generate otomatis berdasarkan agama:

| Agama | Contoh Nama |
|-------|-------------|
| islam | Ahmad, Aisyah, Fatimah, Maryam, Yusuf |
| kristen | Daniel, Ruth, Esther, Samuel, Maria |
| hindu | Arjuna, Sita, Krishna, Dewi, Wayan |
| budha | Ananda, Siddhartha, Tara, Bodhi, Maya |
| default | Rina, Budi, Sari, Andi, Dewi |

### Target Umur (`--ages`)

| Input | Hasil |
|-------|-------|
| `7` | `[6,7,8,9,10]` (min = age-1, max = age+3, cap di 10) |
| `3,4,5,6` | `[3,4,5,6]` (persis sesuai input) |
| (kosong) | `[3,4,5,6,7,8]` (default) |

## Data yang Disimpan

Setiap generate langsung insert ke tabel `activities`:

```json
{
  "type": "storytelling",
  "title": "Judul dari AI",
  "slug": "judul-dari-ai-AbCdE",
  "desc": "Deskripsi singkat cerita",
  "image": "cover.png",
  "moral": "Nilai moral cerita",
  "ages": [6, 7, 8, 9, 10],
  "skills": [],
  "data": {
    "pages": [
      {"num": 1, "text": "Teks halaman 1"},
      {"num": 2, "text": "Teks halaman 2"}
    ]
  },
  "sort_order": 0,
  "active": true,
  "views": 0,
  "status": "pending",
  "agama": ["islam"]
}
```

## Status Workflow

Status default: **`pending`**

```
pending → approved (via developer di frontend)
pending → rejected (via developer di frontend)
```

Developer bisa approve/reject langsung dari halaman Story Telling di frontend.

## Image

- `image` field = `cover.png`
- Pages tidak menyimpan field `image`
- Frontend me-resolve image via `VITE_IMAGES_URL`:
  - Cover: `https://backend.test/images/stories/{id}/cover.png`
  - Page: `https://backend.test/images/stories/{id}/{num}.png`

## Konfigurasi AI

Di `.env` backend:

```env
OPENAI_API_KEY=your-api-key
OPENAI_BASE_URL=https://api.openai.com
```

Model default: `MiniMax-M2.7-highspeed` (configurable via `services.openai.model`).

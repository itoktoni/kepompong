# AGENTS.md - Jejak Tumbuh Frontend (Svelte)

## IMPORTANT: File Edit Rules

**ALWAYS edit files in these directories:**
- `D:\kepompong\frontend\` — frontend code
- `D:\kepompong\backend\` — backend code

**NEVER edit files in:**
- `D:\kepompong\.kilo\worktrees\` — these are worktree copies, not the actual source
- Any `.kilo/` subdirectory

## Project Overview

**Jejak Tumbuh** is a SvelteKit rewrite of the **Langkah Kecil** Vue.js application.
It is a child development tracking PWA (Pendamping Anak).

## Tech Stack

- **Framework**: SvelteKit + Svelte 5 (runes)
- **Styling**: Tailwind CSS 3.4
- **State**: Svelte writable/derived stores
- **Database**: Dexie (IndexedDB)
- **Icons**: Unicode emoji (no web font dependency)
- **Build**: Vite 6
- **PWA**: Custom service worker (SvelteKit `$service-worker` + workbox-precaching)

## Primary Color (Hero Color)

```
rgb(23, 108, 51) / #176C33
```

## Directory Structure

```
src/
├── app.html          # HTML template
├── app.css           # Global CSS (Tailwind + custom)
├── routes/
│   ├── +layout.svelte
│   └── +page.svelte  # Main app page
└── lib/
    ├── assets/       # CSS
    ├── components/   # Reusable UI components
    ├── composables/  # Svelte modules (useInstall, useNotifications, etc.)
    ├── config/       # appConfig.js
    ├── data/         # Static data (pilars, skills, activities, etc.)
    ├── db.js         # IndexedDB (Dexie)
    ├── layouts/      # AppHeader, DesktopHeader, AppSidebar, BottomNav
    ├── pages/        # Page-level components (LoginPage, etc.)
    ├── services/     # API client (api.js)
    ├── stores/       # Svelte stores (app, auth, anak, tools, activity)
    └── utils/        # Utility functions
```

## Vue → Svelte Conversion Reference

| Vue Pattern | Svelte 5 Equivalent |
|---|---|
| `<script setup>` | `<script>` with `$props()`, `$state()`, `$derived()` |
| `ref()` | `$state()` or `writable()` store |
| `computed()` | `$derived()` or `derived()` store |
| `watch()` | `$effect()` |
| `defineProps` | `let { ...props } = $props()` |
| `defineEmits` | callback props |
| `v-if` | `{#if}` |
| `v-for` | `{#each}` |
| `v-show` | CSS `{#if}` or class binding |
| `v-model` | `bind:value` |
| Pinia `defineStore` | `writable()` + exported functions |
| Vue composables | Svelte modules with stores |

## API & Backend

Same backend as Langkah Kecil (Laravel + Sanctum).
API base URL configured via `VITE_API_URL` env variable.

## Activity Card Templates

### Location

```
src/lib/pages/activity/
├── index.js              # Barrel export
├── StoryCard.svelte      # storytelling (with reader modal + TTS)
├── RoleplayCard.svelte   # bermain_peran
├── GameCard.svelte       # permainan
├── ScriptCard.svelte     # monolog
├── ProjectCard.svelte    # proyek_kreatif
├── SongCard.svelte       # musik_gerak
├── PuzzleCard.svelte     # puzzle
├── ExerciseCard.svelte   # mindfulness
├── OutdoorCard.svelte    # outdoor
├── ExperimentCard.svelte # ilmu_pengetahuan
└── WorksheetCard.svelte  # worksheet
```

### How Activity Types Are Defined

In `src/lib/data/activities.js`, each activity type has metadata and a content key:

```js
const defaultMeta = {
  storytelling: { emoji: '📖', title: 'Story Telling', desc: '...', color: '#4CAF50', bg: '#E8F5E9', feature: 'story' },
  bermain_peran: { emoji: '🎭', title: 'Bermain Peran', desc: '...', color: '#FF9800', bg: '#FFF3E0', feature: 'roleplay' },
  // ... etc
}
```

Each type has different content fields normalized from API:

| Type | Content Key | Fields |
|---|---|---|
| storytelling | stories | pages[], moral, image |
| bermain_peran | roles | roles[], pages[] |
| permainan | games | how, rules[] |
| monolog | scripts | script, tips[] |
| proyek_kreatif | projects | duration, difficulty, materials[], steps[] |
| musik_gerak | songs | lyrics, moves[] |
| puzzle | puzzles | questions[] |
| mindfulness | exercises | steps[], benefit |
| outdoor | activities | steps[], observation |
| ilmu_pengetahuan | experiments | materials[], steps[], explanation |
| worksheet | worksheets | (special - uses worksheetTypes) |

### How to Create a New Activity Card

1. **Create card file** in `src/lib/pages/activity/NewCard.svelte`
2. **Props**: `{ item, bg, onclick }` — item has all normalized fields, bg is the background color, onclick opens the detail
3. **Export** from `src/lib/pages/activity/index.js`
4. **Register** in `src/lib/pages/ActivityTab.svelte`:
   - Import the card component
   - Add to `cardMap` object: `{ ..., new_type: NewCard }`

### Card Template Structure

```svelte
<script>
  let { item, bg, onclick } = $props()
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  {onclick}>
  <div class="p-5 flex flex-col flex-1">
    <!-- Header with emoji + badge -->
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {bg}">
        {item.emoji || '🎯'}
      </div>
      <!-- Optional: count badge -->
    </div>
    <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
    {#if item.desc}
      <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
    {/if}
    <!-- Optional: content preview -->
    <!-- Footer action -->
    <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <span class="material-symbols-outlined text-xl">icon_name</span>
      Action Text
      <span class="material-symbols-outlined text-xl ml-auto group-hover:translate-x-1 transition-transform">arrow_forward</span>
    </div>
  </div>
</button>
```

### StoryCard Special Case

StoryCard has an embedded reader modal with:
- Page navigation (swipe + buttons)
- TTS (Text-to-Speech) with Indonesian voice (`id-ID`)
- Moral/lesson screen at the end
- Floating illustration animation

### ActivityTab Integration

In `ActivityTab.svelte`, cards are rendered dynamically:

```svelte
import { StoryCard, RoleplayCard, ... } from './activity/index.js'

const cardMap = {
  storytelling: StoryCard,
  bermain_peran: RoleplayCard,
  // ...
}

<!-- In template -->
{#each sortedItems as item (item.title)}
  {@const Card = cardMap[selectedType?.key]}
  {#if Card}
    <Card {item} bg={selectedType.bg} onclick={() => handleItemClick(item)} />
  {:else}
    <!-- Fallback generic card -->
  {/if}
{/each}
```

## Jadwal Tab (Jadwal Harian)

### File

`src/lib/pages/JadwalTab.svelte`

### Flow Tambah Jadwal

1. User klik "Tambah Jadwal" → modal muncul
2. Isi nama aktivitas + waktu → klik "Simpan"
3. Data disimpan ke **IndexedDB** (Dexie) dan ke **store** di memori (`anakToolsData`)
4. Kalau user login, data juga di-**sync ke server** (via `api.addSchedule`)

### Status Centang (Done)

- **Data jadwal** = permanen, tetap ada sampai dihapus manual
- **Status centang (done)** = reset otomatis setiap hari baru
- Reset dilakukan di `onMount`: cek tanggal hari ini vs `jadwal_last_reset` di localStorage, kalau beda hari → semua `done` direset ke `false`

### Hapus Jadwal

- User klik tombol ✕ → `removeSchedule()` → hapus dari IndexedDB + sync hapus dari server

### History View

- Tombol "History" di header → toggle tampilan antara jadwal aktif (belum selesai) dan jadwal yang sudah dicentang (selesai hari ini)
- Derived state: `doneSchedules` (s.done === true) dan `undoneSchedules` (s.done === false)
- Klik item di history → uncentang → kembali ke daftar aktif

## Checklist Tab

### File

`src/lib/pages/ChecklistTab.svelte`

### Flow Buat Checklist

1. User klik "Buat Checklist" → modal muncul
2. Isi nama checklist → klik "Simpan"
3. Data disimpan ke **IndexedDB** (Dexie) dan ke **store** (`anakToolsData`)
4. Kalau user login, data juga di-**sync ke server** (via `api.addChecklist`)

### Tambah Item

1. Klik "Tambah Item" pada checklist → modal muncul
2. Isi nama aktivitas → klik "Simpan"
3. Item ditambahkan ke checklist yang bersangkutan
4. Simpan ke IndexedDB + sync ke server

### Status Centang (Done)

- **Status centang permanen** — tidak ada reset harian
- Centang/uncentang kapan saja, status tetap tersimpan
- Berbeda dengan JadwalTab yang reset setiap hari

### Progress Bar

- Setiap checklist punya progress bar (persentase item selesai)
- `percent = (item.done / total items) * 100`

### Share Checklist

- Tombol share → format teks: nama checklist + daftar item dengan centang + jumlah selesai
- Gunakan `navigator.share` atau copy ke clipboard

### Hapus

- Hapus checklist: tombol delete → `removeChecklist(index)` → hapus dari IndexedDB + server
- Hapus item: tombol ✕ (muncul saat hover) → `removeChecklistItem()` → hapus dari IndexedDB + server

### Perbedaan dengan JadwalTab

| | JadwalTab | ChecklistTab |
|---|---|---|
| Status centang | Reset otomatis setiap hari | Permanen |
| Tombol History | Ada | Tidak ada |
| Progress bar | Tidak ada | Ada |
| Share | Tidak ada | Ada |

## Share Image (Semua Tab)

### Library

`@zumer/snapdom` — DOM capture engine, convert elemen HTML ke gambar (PNG/JPG/WebP)

### File

`src/lib/utils/share.js`

### Flow Share

1. Bangun HTML card dengan inline style (layout mirip ReferralPage: gradient bg, decorative circles, app branding)
2. Render sementara di DOM (hidden container, posisi off-screen)
3. Capture pakai `snapdom.toBlob()` → hasilnya Blob JPEG
4. Buat `File` object dari Blob
5. Share pakai `navigator.share({ files: [file] })` (Web Share API)
6. Fallback: kalau device tidak support share file → download otomatis sebagai JPG
7. Fallback kedua: kalau capture gagal → share sebagai text biasa

### Tipe Share Card

| Tipe | Fungsi | Konten |
|---|---|---|
| Challenge Progress | `shareProgress()` | Emoji, nama anak, judul challenge, progress bar, poin |
| Challenge Selesai | `shareChallenge()` | Emoji, nama anak, judul, poin tercapai |
| Checklist | `shareChecklistImage()` | Nama anak, judul checklist, daftar item + centang, progress bar |
| Jadwal | `shareJadwalImage()` | Nama anak, daftar jadwal + status done, jumlah selesai |

### Integrasi di Tab

- **ChallengeTab**: tombol share pada setiap challenge card → `shareProgress()` / `shareChallenge()`
- **ChecklistTab**: tombol share pada setiap checklist → `shareChecklistImage()`
- **JadwalTab**: tombol "Share" di header (muncul jika ada jadwal) → `shareJadwalImage()`

## Icons — Unicode Emoji Only

All icons use Unicode emoji. No web font dependency (Material Symbols, Iconify removed).

| Location | Icon Source |
|---|---|
| `sidebarNav.js` | `icon` field is emoji string (🏠🎯📈🏆⏰✅✨) |
| `bottomNav.js` | `icon` field is emoji string (🏠🎯📈👤) |
| `mobileDrawerNav.js` | `icon` field is emoji string |
| `notifications.js` | `icon` field is emoji string |
| All `.svelte` files | Inline emoji in `<span>` tags |

**Do NOT** use `material-symbols-outlined` class or `@iconify/svelte` anywhere.

## Offline-First Architecture

### Principle

The app works fully offline. All data lives in Dexie (IndexedDB). Server calls are only made for explicit user actions (Sync, Download Content). User mutations are queued in `syncQueue` and processed when back online.

### Service Worker

**File**: `src/service-worker.js`

Uses SvelteKit's `$service-worker` module + `workbox-precaching`:

```js
import { build, files, prerendered } from '$service-worker';
import { precacheAndRoute } from 'workbox-precaching';
```

- Precaches all build files, static files, prerendered pages
- `NetworkFirst` for `/api/` calls (falls back to cache)
- `CacheFirst` for Google Fonts
- Registration in `app.html`: only on production (unregisters on localhost for dev)

### Boot Flow (`+page.svelte` onMount)

```
onMount
├── isOffline()?
│   ├── YES → seedAndLoad() directly (no server calls)
│   └── NO  → api.getMe()
│             ├── success → applyServerData() → downloadAllData() → seedAndLoad()
│             └── fail    → seedAndLoad() (fallback to local data)
```

### Data Sources

| Data | Storage | Server Call When |
|---|---|---|
| User profile | `localStorage` (`lk_cache_user`) | On login / `getMe()` |
| Anak list | Dexie `anak` table | On `loadAnakList()` (skipped if offline) |
| Jadwal | Dexie `schedules` table | **Never** on read — only via `queue()` on write |
| Checklist | Dexie `checklists` table | **Never** on read — only via `queue()` on write |
| Challenge | Dexie `challenges` table | **Never** on read — only via `queue()` on write |
| Activities | Dexie `activities` table | Only via explicit Sync/Download button |
| Pilars/Skills | Dexie `settings` table | Only via explicit refresh |

### Sync Queue (`syncService.js`)

All user mutations (add/update/delete) go through `queue(action, payload)`:

```js
import { queue } from '../services/syncService.js'

// Example: add schedule
queue('addSchedule', { anakId, data: { ...item } })
```

The queue:
1. Saves to Dexie `syncQueue` table
2. Processes when online (auto via `online` event listener + 10s poll)
3. Retries with exponential backoff (max 3 attempts)
4. Removes from queue after success

Supported actions: `addAnak`, `updateAnak`, `deleteAnak`, `addSkill`, `deleteSkill`, `resetSkill`, `addActivity`, `addChallenge`, `updateChallenge`, `deleteChallenge`, `addChecklist`, `updateChecklist`, `deleteChecklist`, `addSchedule`, `updateSchedule`, `deleteSchedule`, `toggleScheduleDone`, `addWorksheet`, `deleteWorksheet`, `addEvaluation`, `trackView`

### Activity Data Flow

```
Login → activities_grouped in response → saved to localStorage
On refresh → getMe() returns activities_grouped
           → downloadAllData() only saves to Dexie if Dexie is empty
           → seedAndLoad() loads from Dexie

User clicks "Download Content" → GET /api/activities?grouped=1
                                → saveActivities() to Dexie (replaces all)

User clicks "Sync" (per type) → GET /api/activities/sync/{type}
                               → saveActivitiesByType() to Dexie (replaces that type only)

Opening activity list → reads from store (populated from Dexie)
                       → NO server call

Opening activity detail (StoryCard, etc.) → NO server call
Clicking "Selesai" → trackView (online) or queue('trackView') (offline)
```

### Backend Endpoints

| Endpoint | Method | Auth | Description |
|---|---|---|---|
| `/api/activities` | GET | No | List all activities (supports `?grouped=1`, `?type=X`) |
| `/api/activities/sync/{type}` | GET | No | Get activities by type in grouped format |
| `/api/activities/type/{type}` | GET | No | Get activities by type (flat array) |
| `/api/activities/{id}/view` | GET | No | Track view count |
| `/api/me` | GET | Yes | User data + `activities_grouped` |

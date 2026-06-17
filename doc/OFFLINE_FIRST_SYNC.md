# Jejak Tumbuh - Offline-First Sync Documentation

## Overview

Jejak Tumbuh menggunakan arsitektur **offline-first** untuk semua operasi CRUD. Data disimpan ke **Dexie (IndexedDB)** terlebih dahulu, kemudian di-queue untuk sync ke server di background.

### Prinsip Utama

1. **Dexie adalah source of truth** — UI selalu baca dari Dexie, bukan dari server
2. **Tidak ada direct hit ke server** — semua operasi simpan ke Dexie + queue
3. **Sync di background** — setiap 10 detik atau saat switch tab
4. **Server data tidak menimpa local** — jika ada perubahan lokal yang belum tersync

---

## Arsitektur

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   UI/Store   │────▶│  Dexie DB   │     │   Server    │
│ (anakTools)  │     │ (IndexedDB) │     │  (Laravel)  │
└─────────────┘     └──────┬──────┘     └──────▲──────┘
                           │                    │
                           │              ┌─────┴─────┐
                           └─────────────▶│ Sync Queue │
                                          │ Processor  │
                                          └───────────┘
```

---

## Dexie Schema (Version 5)

```js
db.version(5).stores({
  anak: '++id, nama',
  challenges: '++id, anakId, category',
  challengeHistory: '++id, anakId, category',
  checklists: '++id, anakId',
  schedules: '++id, anakId',
  scheduleHistories: '++id, anakId, scheduleId, date',
  worksheets: '++id, anakId',
  settings: 'key',
  syncQueue: '++id, action, timestamp, status'
})
```

### Tabel `syncQueue`

| Field | Type | Description |
|-------|------|-------------|
| `id` | auto-increment | Primary key |
| `action` | string | Nama action (e.g. `addChallenge`, `updateChallenge`) |
| `payload` | object | Data yang di-sync |
| `timestamp` | number | Waktu queue dibuat |
| `attempts` | number | Jumlah percobaan sync |
| `status` | string | `pending` atau `synced` |

---

## Flow Operasi

### 1. Create Challenge

**File**: `toolsStore.js` → `addChallenge()`

```
User klik "Tambah Challenge"
  │
  ├─▶ Store: push item ke anakToolsData
  ├─▶ Dexie: dbSaveChallenge({ ...item, anakId })
  └─▶ Queue: queue('addChallenge', { anakId, data })
        │
        └─▶ Sync (background): POST /api/anak/{id}/challenges
              └─▶ Simpan serverId ke Dexie + dispatch sync-id-update
```

### 2. Edit Challenge

**File**: `toolsStore.js` → `editChallenge()`

```
User edit challenge
  │
  ├─▶ Store: Object.assign(c, data)
  ├─▶ Dexie: dbSaveChallenge({ ...c, dirty: true })
  └─▶ Queue: queue('updateChallenge', { anakId, challengeId: c.serverId, data })
        │
        └─▶ Sync (background): PUT /api/anak/{id}/challenges/{challengeId}
              └─▶ Hapus flag dirty dari Dexie
```

### 3. Delete Challenge

**File**: `toolsStore.js` → `deleteChallenge()`

```
User klik hapus
  │
  ├─▶ Store: splice dari challenges array
  ├─▶ Dexie: dbRemoveChallenge(id)
  └─▶ Queue: queue('deleteChallenge', { anakId, challengeId: removed.serverId })
        │
        └─▶ Sync (background): DELETE /api/anak/{id}/challenges/{challengeId}
```

### 4. Tambah Point (+1)

**File**: `toolsStore.js` → `addPoint()`

```
User klik "+1 Poin"
  │
  ├─▶ Store: c.points = Math.min(c.maxPoints, c.points + 1)
  ├─▶ Dexie: dbSaveChallenge({ ...c, dirty: true })
  └─▶ Queue: queue('updateChallenge', { anakId, challengeId: c.serverId, data: { points } })
        │
        └─▶ Sync (background): PUT /api/anak/{id}/challenges/{challengeId}
              └─▶ Hapus flag dirty dari Dexie
```

### 5. Kurang Point (-1)

**File**: `toolsStore.js` → `removePoint()`

```
User klik "-1 Poin"
  │
  ├─▶ Store: c.points = Math.max(0, c.points - 1)
  ├─▶ Dexie: dbSaveChallenge({ ...c, dirty: true })
  └─▶ Queue: queue('updateChallenge', { anakId, challengeId: c.serverId, data: { points } })
        │
        └─▶ Sync (background): PUT /api/anak/{id}/challenges/{challengeId}
              └─▶ Hapus flag dirty dari Dexie
```

---

## Refresh Behavior

### Problem

Saat refresh, `downloadAllData()` memanggil `syncServerData()` yang bisa menimpa data lokal.

### Solution

1. **`syncServerData()` tidak menyentuh challenges** — hanya update anak record (nama, emoji, dll)
2. **`refreshChallenges()` cek Dexie dulu** — jika ada data di Dexie, load dari sana
3. **Flag `dirty`** — challenge yang diubah lokal punya `dirty: true`, tidak ditimpa server

### Flow Refresh

```
Refresh
  │
  ├─▶ getMe() → downloadAllData() → syncServerData()
  │     ├─▶ Update anak record ✓
  │     ├─▶ SKIP challenges (tidak dihapus/ditulis)
  │     └─▶ Update checklists, schedules, worksheets (jika tidak ada pending queue)
  │
  └─▶ seedAndLoad() → loadToolsData()
        └─▶ Baca challenges dari Dexie → points tetap ✓
```

---

## Sync Queue Processor

**File**: `syncService.js` → `processSyncQueue()`

### Trigger

- Setiap **10 detik** (polling via `setInterval`)
- Saat **switch tab** (dipanggil dari `appStore.switchTab()`)
- Saat **network kembali online** (event `online`)

### Flow

```
processSyncQueue()
  │
  ├─▶ Cek: offline? → skip
  ├─▶ Cek: not authenticated? → skip
  ├─▶ clearSyncedQueue() — hapus item status='synced'
  ├─▶ getSyncQueue() — ambil item status='pending'
  │
  └─▶ Loop setiap entry:
        │
        ├─▶ attempts >= 3? → drop (delete dari queue)
        ├─▶ nextRetryAt belum tercapai? → skip
        │
        └─▶ executeAction(entry)
              │
              ├─▶ Berhasil:
              │     ├─▶ markSyncQueueDone(id) — status = 'synced'
              │     └─▶ removeSyncQueueItem(id) — hapus dari Dexie
              │
              └─▶ Gagal:
                    ├─▶ attempts < 3? → update attempts + nextRetryAt
                    └─▶ attempts >= 3? → drop (delete dari queue)
```

### Retry Logic

- **Max attempts**: 3
- **Backoff**: `5000 * 3^attempts` ms
  - Attempt 1: 5 detik
  - Attempt 2: 15 detik
  - Attempt 3: 45 detik → drop

---

## getServerAnakId

**File**: `syncService.js` → `getServerAnakId()`

Fungsi untuk resolve local anak ID ke server anak ID.

### Flow

```
getServerAnakId(localId)
  │
  ├─▶ Cek local record: punya serverId? → return
  ├─▶ Cek queue: ada pending 'addAnak'? → throw (tunggu)
  ├─▶ Fetch server list: cari match by nama+tgl+bln+thn → simpan serverId & return
  └─▶ Tidak ketemu? → create baru di server → simpan serverId & return
```

---

## Status Queue

### Status Values

| Status | Description |
|--------|-------------|
| `pending` | Menunggu untuk di-sync |
| `synced` | Sudah berhasil di-sync, siap dihapus |

### Cleanup

- `clearSyncQueue()` — hapus semua item
- `clearSyncedQueue()` — hapus item status='synced'
- `markSyncQueueDone(id)` — update status ke 'synced'

---

## Pending Count Badge

**File**: `syncStatusStore.js` + `AppHeader.svelte`

### Inisialisasi

Saat app load, `initSyncListener()` membaca jumlah pending queue dari Dexie dan set ke store:

```js
const initialCount = await getSyncQueueCount()
if (initialCount > 0) setPending(initialCount)
```

### Display

Badge di header menampilkan:
- ☁️ `cloud_sync` — tersinkronkan
- 🔄 `sync` (spinning) — sedang menyinkronkan
- 📴 `cloud_off` — mode offline
- Angka — jumlah pending items

---

## Protected Tables

Tabel yang tidak ditimpa oleh `syncServerData()` saat refresh:

| Tabel | Protected | Alasan |
|-------|-----------|--------|
| `challenges` | ✅ | Punya dirty flag, offline-first |
| `challengeHistory` | ❌ | Ditulis oleh server |
| `checklists` | ❌ | Bisa ditimpa jika tidak ada pending queue |
| `schedules` | ❌ | Bisa ditimpa jika tidak ada pending queue |
| `worksheets` | ❌ | Bisa ditimpa jika tidak ada pending queue |

---

## API Endpoints yang Digunakan

| Method | Endpoint | Action |
|--------|----------|--------|
| GET | `/api/anak/{id}/challenges` | Ambil challenges |
| POST | `/api/anak/{id}/challenges` | Create challenge |
| PUT | `/api/anak/{id}/challenges/{id}` | Update challenge |
| DELETE | `/api/anak/{id}/challenges/{id}` | Delete challenge |
| GET | `/api/anak/{id}/challenge-history` | Ambil history |
| POST | `/api/anak/{id}/challenge-history` | Create history |

---

## File yang Berubah

| File | Perubahan |
|------|-----------|
| `stores/toolsStore.js` | Hapus `canSync()` direct hit, semua offline-first |
| `stores/anakStore.js` | Hapus `canSync()` direct hit, semua offline-first |
| `services/syncService.js` | Tambah `getServerAnakId()`, `markSyncQueueDone()`, `clearSyncedQueue()` |
| `db.js` | Tambah version 5 (status index), `markSyncQueueDone()`, `clearSyncedQueue()`, skip challenges di `syncServerData()` |

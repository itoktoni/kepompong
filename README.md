# Langkah Kecil - API Documentation

## Overview

**Langkah Kecil** is a child development tracking application with a **Vue.js 3 SPA frontend** and **Laravel REST API backend**. The frontend communicates with the backend via REST API using Bearer token authentication (Laravel Sanctum).

| Component | Stack | Location |
|-----------|-------|----------|
| **Backend** | Laravel 13, PHP 8.3, MariaDB, Sanctum | `backend/` |
| **Frontend** | Vue 3, Pinia, Dexie.js, Tailwind CSS, Vite PWA | `frontend/` |

---

## Architecture

```
┌─────────────────────┐         REST API          ┌─────────────────────┐
│                     │  ──────────────────────►   │                     │
│   Vue.js Frontend   │   Bearer Token (Sanctum)  │  Laravel Backend    │
│   (PWA / SPA)       │  ◄──────────────────────   │  (API Server)       │
│                     │        JSON                │                     │
└─────────────────────┘                            └─────────────────────┘
         │                                                   │
         ▼                                                   ▼
   Dexie.js (IndexedDB)                              MariaDB Database
   Local-first storage                               Cloud persistence
```

### Communication Flow

1. **Frontend** sends HTTP requests to `VITE_API_URL` (e.g., `https://hermes.itoktoni.com/api`)
2. **Backend** validates requests via Laravel Sanctum Bearer tokens
3. **Backend** returns JSON responses
4. **Frontend** stores data locally in IndexedDB (Dexie.js) as offline-first cache
5. When authenticated, frontend syncs local data to server via `/langkahkecil/sync`

### Environment Variables

**Frontend** (`.env`):
```
VITE_API_URL=https://hermes.itoktoni.com/api
VITE_APP_NAME=Teman Kecil
VITE_BACKEND_NAME=Startok
```

**Backend** (`.env`):
```
DB_CONNECTION=mariadb
DB_DATABASE=startok
SANCTUM_STATEFUL_DOMAINS=startok.test,localhost:5173,localhost:8000
```

---

## Authentication

All protected endpoints require a `Bearer` token in the `Authorization` header.

```
Authorization: Bearer <access_token>
```

---

## API Endpoints

### Auth

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/login` | No | Login, returns token + user + anak_list |
| `POST` | `/register` | No | Register new user |
| `POST` | `/logout` | Yes | Revoke current token |
| `GET` | `/me` | Yes | Get current user info |
| `PUT` | `/profile` | Yes | Update name/email |
| `PUT` | `/password` | Yes | Change password |

### Children (Anak)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/langkahkecil/anak` | Yes | List all children with relations |
| `POST` | `/langkahkecil/anak` | Yes | Create a new child |
| `PUT` | `/langkahkecil/anak/{id}` | Yes | Update a child |
| `DELETE` | `/langkahkecil/anak/{id}` | Yes | Delete a child |

### Skills

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/skills` | Yes | Add/update skill (upsert by key) |
| `PUT` | `/langkahkecil/anak/{anakId}/skills/{skillId}` | Yes | Update skill progress/title |
| `DELETE` | `/langkahkecil/anak/{anakId}/skills/{skillId}` | Yes | Delete a skill |

### Skill Activities

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/activities` | Yes | Add activity to a skill |
| `DELETE` | `/langkahkecil/anak/{anakId}/activities/{activityId}` | Yes | Delete an activity |
| `PUT` | `/langkahkecil/anak/{anakId}/activities/{activityId}/toggle` | Yes | Toggle activity completion |

### Completed Skills

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/completed-skills` | Yes | Mark skill as completed |
| `DELETE` | `/langkahkecil/anak/{anakId}/completed-skills/{key}` | Yes | Remove completed skill |

### Challenges

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/challenges` | Yes | Create a challenge |
| `PUT` | `/langkahkecil/anak/{anakId}/challenges/{challengeId}` | Yes | Update a challenge |
| `DELETE` | `/langkahkecil/anak/{anakId}/challenges/{challengeId}` | Yes | Delete a challenge |

### Challenge History

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/challenge-history` | Yes | Record challenge completion |

### Checklists

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/checklists` | Yes | Create a checklist |
| `PUT` | `/langkahkecil/anak/{anakId}/checklists/{checklistId}` | Yes | Update a checklist |
| `DELETE` | `/langkahkecil/anak/{anakId}/checklists/{checklistId}` | Yes | Delete a checklist |

### Schedules

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/schedules` | Yes | Create a schedule |
| `PUT` | `/langkahkecil/anak/{anakId}/schedules/{scheduleId}` | Yes | Update a schedule |
| `DELETE` | `/langkahkecil/anak/{anakId}/schedules/{scheduleId}` | Yes | Delete a schedule |

### Worksheets

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/anak/{anakId}/worksheets` | Yes | Save a worksheet |
| `DELETE` | `/langkahkecil/anak/{anakId}/worksheets/{worksheetId}` | Yes | Delete a worksheet |

### Evaluations

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/langkahkecil/anak/{anakId}/evaluations` | Yes | Get evaluations with stats |
| `POST` | `/langkahkecil/anak/{anakId}/evaluations` | Yes | Add/update evaluation |
| `DELETE` | `/langkahkecil/anak/{anakId}/evaluations/{evalId}` | Yes | Delete an evaluation |

### Sync

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/langkahkecil/sync` | Yes | Full data sync (bulk upload) |

### Activities (Master Data)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/activities` | No | List activity templates |
| `GET` | `/activities/types` | No | List activity types |

### Push Notifications

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/push/vapid-key` | No | Get VAPID public key |
| `POST` | `/push/subscribe` | Yes | Subscribe to push |
| `POST` | `/push/unsubscribe` | Yes | Unsubscribe from push |
| `GET` | `/push/status` | Yes | Get subscription status |
| `POST` | `/push/send` | Yes | Send push notification |
| `POST` | `/push/send-to-all` | Yes | Broadcast push notification |

### Notifications

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/notifications` | Yes | List notifications |
| `PUT` | `/notifications/{id}/read` | Yes | Mark as read |
| `PUT` | `/notifications/read-all` | Yes | Mark all as read |
| `DELETE` | `/notifications/{id}` | Yes | Delete notification |
| `DELETE` | `/notifications` | Yes | Clear all notifications |

---

## Request/Response Examples

### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "access_token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John",
    "email": "user@example.com",
    "role": "user"
  },
  "anak_list": [...]
}
```

### Create Child

```http
POST /api/langkahkecil/anak
Authorization: Bearer <token>
Content-Type: application/json

{
  "nama": "Anakku",
  "gender": "male",
  "umur": 5,
  "tanggal_lahir": 15,
  "bulan_lahir": 6,
  "tahun_lahir": 2021,
  "emoji": "👦"
}
```

### Add Skill

```http
POST /api/langkahkecil/anak/1/skills
Authorization: Bearer <token>
Content-Type: application/json

{
  "key": "motorik-kasar",
  "emoji": "🏃",
  "title": "Motorik Kasar",
  "pilar": "fisik",
  "color": "#6DBE7B"
}
```

### Full Sync

```http
POST /api/langkahkecil/sync
Authorization: Bearer <token>
Content-Type: application/json

{
  "anak_list": [
    {
      "nama": "Anakku",
      "gender": "male",
      "umur": 5,
      "tahun_lahir": 2021,
      "emoji": "👦",
      "skills": [...],
      "completed_skills": [...],
      "challenges": [...],
      "checklists": [...],
      "schedules": [...],
      "worksheets": [...]
    }
  ]
}
```

---

## Database Schema (Backend)

### `langkah_kecil_anak`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | FK to users |
| `nama` | string | Child name |
| `gender` | string | Gender |
| `umur` | integer | Age |
| `tanggal_lahir` | integer | Birth day (1-31) |
| `bulan_lahir` | integer | Birth month (1-12) |
| `tahun_lahir` | integer | Birth year |
| `emoji` | string | Avatar emoji |
| `avatar` | string | Avatar URL |
| `settings` | json | Custom settings |

### `langkah_kecil_skills`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `key` | string | Skill identifier (e.g., "motorik-kasar") |
| `emoji` | string | Skill emoji |
| `title` | string | Skill name |
| `pilar` | string | Pillar category |
| `progress` | integer | Progress 0-100 |
| `color` | string | Display color |

### `langkah_kecil_skill_activities`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `skill_id` | bigint | FK to skills |
| `title` | string | Activity title |
| `emoji` | string | Activity emoji |
| `feature` | string | Feature type |
| `date` | string | Date string |
| `completed` | boolean | Completion status |

### `langkah_kecil_completed_skills`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `key` | string | Skill identifier |
| `emoji` | string | Emoji |
| `title` | string | Skill name |
| `pilar` | string | Pillar |
| `color` | string | Color |
| `completed_at` | timestamp | Completion time |

### `langkah_kecil_challenges`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `category` | string | Challenge category |
| `title` | string | Challenge title |
| `emoji` | string | Emoji |
| `points` | integer | Current points |
| `status` | string | pending/completed/cancelled |
| `date` | string | Date |
| `meta` | json | Extra data (maxPoints, etc.) |

### `langkah_kecil_challenge_history`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `category` | string | Category |
| `title` | string | Title |
| `date` | string | Date |
| `meta` | json | Extra data |

### `langkah_kecil_checklists`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `title` | string | Checklist title |
| `items` | json | Checklist items |
| `date` | string | Date |

### `langkah_kecil_schedules`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `label` | string | Schedule label |
| `time` | string | Time string |
| `done` | boolean | Completion status |
| `date` | string | Date |

### `langkah_kecil_worksheets`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `type` | string | Worksheet type |
| `data` | json | Worksheet content |
| `date` | string | Date |

### `langkah_kecil_evaluations`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `anak_id` | bigint | FK to anak |
| `skill_key` | string | Skill identifier |
| `skill_title` | string | Skill name |
| `pilar` | string | Pillar |
| `points` | integer | Points scored |
| `max_points` | integer | Maximum points |
| `notes` | string | Notes |

---

## Frontend Architecture

### Tech Stack

- **Vue 3** with Composition API (`<script setup>`)
- **Pinia** for state management
- **Dexie.js** (IndexedDB) for local-first offline storage
- **Tailwind CSS** with Material Design 3 color tokens
- **Vite PWA** for installable progressive web app
- **Iconify** for icons

### Key Stores (Pinia)

| Store | File | Responsibility |
|-------|------|----------------|
| `useAppStore` | `stores/appStore.js` | UI state, active tab, navigation |
| `useAuthStore` | `stores/authStore.js` | Authentication, token management |
| `useAnakStore` | `stores/anakStore.js` | Children CRUD, offline-first sync |
| `useToolsStore` | `stores/toolsStore.js` | Challenges, checklists, schedules |
| `useActivityStore` | `stores/activityStore.js` | Activity templates cache |

### API Service (`services/api.js`)

The frontend API client uses native `fetch()` with:
- Automatic `Authorization: Bearer <token>` header
- Token stored in `localStorage` (`lk_auth_token`)
- 401 responses auto-clear token
- Base URL from `VITE_API_URL` env variable

### Offline-First Strategy

1. All data is stored locally in IndexedDB via Dexie.js
2. When authenticated + `autoSync` enabled, writes go to both local DB and API
3. On login, server data is synced to local DB via `syncServerData()`
4. Local-only data is uploaded via `/langkahkecil/sync` endpoint
5. App works fully offline with local data

### Pages

| Page | Component | Description |
|------|-----------|-------------|
| Pilar | `PilarTab.vue` | Development pillars (Fisik, Kognitif, etc.) |
| Progress | `ProgressTab.vue` | Skill progress statistics |
| Activity | `ActivityTab.vue` | Activity suggestions by type |
| Profile | `ProfileTab.vue` | User profile, child management |
| Settings | `SettingsTab.vue` | App settings, sync config |
| Billing | `BillingTab.vue` | Subscription management |
| Challenge | `ChallengePage.vue` | Gamified challenges |
| Jadwal | `JadwalPage.vue` | Daily schedule |
| Checklist | `ChecklistPage.vue` | Daily checklist |
| Worksheet | `WorksheetPage.vue` | Printable learning worksheets |

---

## Development

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

### Proxy Configuration

The frontend Vite dev server proxies `/api` requests to the backend:

```js
// frontend/vite.config.js
proxy: {
  '/api': {
    target: 'https://startok.test',
    changeOrigin: true,
    secure: false,
  }
}
```

In production, `VITE_API_URL` points to the deployed backend API.

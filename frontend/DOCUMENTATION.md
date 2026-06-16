# Jejak Tumbuh - Svelte Frontend Documentation

## Overview

**Jejak Tumbuh** is a child development tracking PWA built with SvelteKit. It's a rewrite of the **Langkah Kecil** Vue.js application.

## Tech Stack

- **Framework**: SvelteKit + Svelte 5 (runes)
- **Styling**: Tailwind CSS 3.4
- **State**: Svelte writable/derived stores
- **Database**: Dexie (IndexedDB)
- **Icons**: Material Symbols Outlined + @iconify/svelte
- **Build**: Vite 6
- **PWA**: vite-plugin-pwa

## Primary Color

```
rgb(23, 108, 51) / #176C33
```

## Directory Structure

```
src/
├── app.html              # HTML template
├── app.css               # Global CSS (Tailwind + custom)
├── routes/
│   ├── +layout.svelte
│   ├── +page.svelte      # Main app page
│   └── reset-password/
│       └── +page.svelte  # Reset password page
└── lib/
    ├── assets/           # CSS
    ├── components/       # Reusable UI components
    │   ├── AppButton.svelte
    │   ├── AppInput.svelte
    │   ├── AppModal.svelte
    │   ├── UpgradeModal.svelte
    │   ├── AnakSelector.svelte
    │   ├── NotificationDropdown.svelte
    │   └── SyncModal.svelte
    ├── composables/      # Svelte modules
    ├── config/
    │   └── appConfig.js  # App configuration from .env
    ├── data/             # Static data
    │   ├── sidebarNav.js
    │   ├── bottomNav.js
    │   ├── pilars.js
    │   └── activities.js
    ├── db.js             # IndexedDB (Dexie)
    ├── layouts/          # Layout components
    │   ├── AppHeader.svelte
    │   ├── DesktopHeader.svelte
    │   ├── AppSidebar.svelte
    │   └── BottomNav.svelte
    ├── pages/            # Page-level components
    │   ├── LoginPage.svelte
    │   ├── ProfileTab.svelte
    │   ├── PilarTab.svelte
    │   ├── ActivityTab.svelte
    │   ├── ProgressTab.svelte
    │   ├── SettingsTab.svelte
    │   ├── BillingTab.svelte
    │   └── ReferralTab.svelte
    ├── services/
    │   └── api.js        # API client
    ├── stores/           # Svelte stores
    │   ├── appStore.js
    │   ├── authStore.js
    │   ├── anakStore.js
    │   ├── toolsStore.js
    │   └── activityStore.js
    └── utils/            # Utility functions
```

## Environment Variables

### .env (Production)

```env
VITE_APP_NAME=Jejak Tumbuh
VITE_APP_TAGLINE=Pendamping Anak
VITE_APP_URL=https://jejakTumbuh.itoktoni.com
VITE_API_URL=https://hermes.itoktoni.com/api
VITE_BACKEND_NAME=Startok
```

### .env.development

```env
VITE_APP_NAME=Jejak Tumbuh
VITE_APP_TAGLINE=Pendamping Anak
VITE_APP_URL=http://localhost:5173
VITE_API_URL=https://backend.test/api
VITE_BACKEND_NAME=Server
```

## API Endpoints

Base URL: `VITE_API_URL`

### Auth

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/login` | User login |
| POST | `/register` | User registration |
| POST | `/forgot-password` | Request password reset |
| POST | `/reset-password` | Reset password with token |
| POST | `/logout` | User logout |
| GET | `/me` | Get current user |
| PUT | `/profile` | Update profile |
| PUT | `/password` | Change password |

### Anak (Child)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/anak` | List all children |
| POST | `/anak` | Add new child |
| PUT | `/anak/{id}` | Update child |
| DELETE | `/anak/{id}` | Delete child |

### Skills

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/anak/{id}/skills` | Add skill |
| PUT | `/anak/{id}/skills/{skillId}` | Update skill |
| DELETE | `/anak/{id}/skills/{skillId}` | Delete skill |

### Activities

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/anak/{id}/activities` | Add activity |
| DELETE | `/anak/{id}/activities/{activityId}` | Delete activity |
| PUT | `/anak/{id}/activities/{activityId}/toggle` | Toggle activity |

### Completed Skills

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/anak/{id}/completed-skills` | Mark skill as completed |
| DELETE | `/anak/{id}/completed-skills/{key}` | Undo completed skill |

### Challenges

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/anak/{id}/challenges` | Add challenge |
| PUT | `/anak/{id}/challenges/{challengeId}` | Update challenge |
| DELETE | `/anak/{id}/challenges/{challengeId}` | Delete challenge |

### Checklists

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/anak/{id}/checklists` | Add checklist |
| PUT | `/anak/{id}/checklists/{checklistId}` | Update checklist |
| DELETE | `/anak/{id}/checklists/{checklistId}` | Delete checklist |

### Schedules

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/anak/{id}/schedules` | Add schedule |
| PUT | `/anak/{id}/schedules/{scheduleId}` | Update schedule |
| DELETE | `/anak/{id}/schedules/{scheduleId}` | Delete schedule |

### Worksheets

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/anak/{id}/worksheets` | Add worksheet |
| DELETE | `/anak/{id}/worksheets/{worksheetId}` | Delete worksheet |

### Evaluations

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/anak/{id}/evaluations` | List evaluations |
| POST | `/anak/{id}/evaluations` | Add evaluation |
| DELETE | `/anak/{id}/evaluations/{evalId}` | Delete evaluation |

### Billing

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/plans` | List available plans |
| POST | `/payments` | Create payment |
| GET | `/payments/{id}` | Get payment status |
| POST | `/payments/{id}/settle` | Settle payment |
| POST | `/payments/{id}/cancel` | Cancel payment |
| POST | `/payments/validate-discount` | Validate discount code |

### Other

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/activities` | List activities |
| GET | `/activities/types` | List activity types |
| GET | `/referrals` | List referrals |
| GET | `/discounts` | List discounts |
| POST | `/discounts` | Create discount |
| DELETE | `/discounts/{id}` | Delete discount |
| POST | `/cashout` | Request cashout |
| GET | `/cashouts` | List cashouts |
| GET | `/notifications` | List notifications |
| PUT | `/notifications/{id}/read` | Mark notification as read |
| PUT | `/notifications/read-all` | Mark all as read |
| DELETE | `/notifications/{id}` | Delete notification |
| DELETE | `/notifications` | Clear all notifications |
| POST | `/sync` | Sync data to server |

## State Management

### Stores

- **appStore.js** - App state (active tab, selected child, etc.)
- **authStore.js** - Authentication state (user, token, plans)
- **anakStore.js** - Children data management
- **toolsStore.js** - Tools data
- **activityStore.js** - Activity data

### Usage

```javascript
import { user, token, isAuthenticated } from '../stores/authStore.js'
import { anakList, addAnak } from '../stores/anakStore.js'
import { switchTab, selectedAnakId } from '../stores/appStore.js'
```

## Components

### AppInput

```svelte
<AppInput
  id="email"
  type="email"
  label="Email"
  placeholder="email@contoh.com"
  bind:value={email}
  required
  error={validationErrors?.email?.[0] || ''}
  passwordToggle
/>
```

Props:
- `type` - Input type (text, email, password, etc.)
- `label` - Field label
- `placeholder` - Placeholder text
- `error` - Error message
- `passwordToggle` - Show password toggle button

### AppModal

```svelte
<AppModal show={showModal} title="Modal Title" onclose={() => showModal = false}>
  <p>Modal content</p>
</AppModal>
```

### AppButton

```svelte
<AppButton variant="primary" loading={isLoading} onclick={handleClick}>
  Click Me
</AppButton>
```

### UpgradeModal

```svelte
<UpgradeModal show={showUpgrade} onclose={() => showUpgrade = false} />
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

## Development

### Setup

```bash
cd svelte
npm install
npm run dev
```

### Build

```bash
npm run build
```

### Preview

```bash
npm run preview
```

## Database Schema (IndexedDB)

Managed by Dexie.js. See `src/lib/db.js` for schema definition.

### Tables

- **anak** - Children data
- **settings** - App settings
- **cache** - API response cache

## PWA Features

- Offline support
- Install prompt
- Push notifications
- Background sync

## User Flow

### 1. Login / Register

```
LoginPage.svelte
├── Toggle Masuk/Daftar
├── Login: email + password → POST /login
├── Register: name + email + phone + password → POST /register
├── Lupa Password → POST /forgot-password → email reset link
└── Reset Password → /reset-password?token=xxx&email=xxx
```

### 2. After Login

```
+page.svelte
├── Fetch /me → user data
├── Fetch /anak → anak list
├── Load plans, discounts, trial info
└── Redirect to Activity tab (default)
```

### 3. Profile Tab

```
ProfileTab.svelte
├── Profile Card (name, email, phone, gender, agama)
│   └── Edit Profile → PUT /profile
├── Ganti Password → PUT /password
├── Anak List
│   ├── Belum ada anak → Tampilkan empty state
│   ├── Klik card anak → Edit Anak Modal
│   │   ├── Edit nama, gender, agama, tanggal lahir
│   │   └── Simpan → PUT /anak/{id}
│   └── Klik "Tambah"
│       ├── Check kuota (plan_value, trial expiry)
│       │   ├── Tanpa plan → Tampilkan Upgrade Popup
│       │   ├── Trial expired → Tampilkan Upgrade Popup
│       │   └── Anak >= maxChildren → Tampilkan Upgrade Popup
│       │       ├── "Nanti Saja" → Tutup popup
│       │       └── "Lihat Paket" → Navigate ke Billing tab
│       └── Kuota tersedia → Tampilkan Add Anak Modal
│           ├── Isi: nama, gender, agama, tanggal lahir
│           └── Simpan → POST /anak
├── Logout → clear stores
└── Upgrade Popup (inline, bukan modal)
    ├── Icon: workspace_premium
    ├── Teks: "Kamu sudah mencapai batas X anak untuk paket yang kamu pilih saat ini."
    └── Tombol: "Nanti Saja" / "Lihat Paket"
```

### 4. Add Anak Flow

```
Klik "Tambah"
├── Check: userRole === 'developer'?
│   └── YES → Langsung buka Add Anak Modal (tanpa batas)
├── Check: userPlan ada?
│   └── NO → Show Upgrade Popup → "Lihat Paket" → Billing tab
├── Check: trial expired?
│   └── YES → Show Upgrade Popup → "Lihat Paket" → Billing tab
├── Check: anakList.length >= maxChildren (plan_value)?
│   ├── YES → Show Upgrade Popup
│   │   ├── "Nanti Saja" → Tutup popup
│   │   └── "Lihat Paket" → switchTab('billing')
│   └── NO → Show Add Anak Modal
│       ├── Form: nama, gender, agama, tanggal lahir
│       ├── Klik "Simpan"
│       │   ├── POST /anak
│       │   ├── Reload anakList dari server
│       │   └── Tutup modal
│       └── Error → Tampilkan error
```

### 5. Upgrade Flow

```
Upgrade Popup (inline di ProfileTab)
├── Muncul saat kuota habis atau trial expired
├── Tampilkan:
│   ├── Icon workspace_premium
│   ├── "Kamu sudah mencapai batas X anak"
│   │   └── X = userPlan.plan_value (default: 1)
│   └── Tombol:
│       ├── "Nanti Saja" → Tutup popup
│       └── "Lihat Paket" → switchTab('billing')
└── Billing Tab → Checkout → Bayar via QRIS
```

### 6. Sync Flow

```
Klik sync button
├── Anak list → POST /sync (upload ke server)
├── Server response → update local data
└── SyncModal tampilkan progress
```

### 7. Tab Navigation

```
AppHeader (mobile) → Klik menu → AppSidebar (mobile drawer)
AppSidebar (desktop) → Klik menu → switchTab
BottomNav (mobile) → Klik tab → switchTab

Tabs:
├── pilar → PilarTab (home)
├── activity → ActivityTab
├── progress → ProgressTab
├── challenge → ChallengeTab
├── jadwal → ScheduleTab
├── checklist → ChecklistTab
├── profile → ProfileTab
├── settings → SettingsTab
├── billing → BillingTab
└── referral → ReferralTab
```

## Key Features

1. **Multi-child tracking** - Track multiple children's development
2. **Skills management** - Add, track, and complete skills
3. **Activities logging** - Log daily activities
4. **Challenges** - Create and track challenges
5. **Checklists** - Custom checklists
6. **Schedules** - Daily schedules
7. **Worksheets** - Educational worksheets
8. **Evaluations** - Skill evaluations
9. **Sync** - Cloud sync with server
10. **Billing** - Subscription management
11. **Affiliate** - Referral program

## File Naming Convention

- Components: `PascalCase.svelte` (e.g., `AppButton.svelte`)
- Pages: `PascalCase.svelte` (e.g., `ProfileTab.svelte`)
- Stores: `camelCase.js` (e.g., `authStore.js`)
- Utils: `camelCase.js` (e.g., `helpers.js`)

## Styling

- Uses Tailwind CSS utility classes
- Custom colors defined in `tailwind.config.js`
- Material Symbols Outlined for icons
- Responsive design with mobile-first approach
- Border style: `border-4 border-[#B7D9BC]`
- Card style: `rounded-[24px]` or `rounded-[32px]`
- Primary button: `btn-pop-green` (shadow effect)
- Secondary button: `btn-pop-gray` (shadow effect)

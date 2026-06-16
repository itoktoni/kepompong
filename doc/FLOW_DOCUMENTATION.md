# Jejak Tumbuh - Complete Flow Documentation

## 1. Login Flow

### Backend: `POST /api/login`
- **Controller**: `AuthController::login()`
- Validates email + password
- Returns `access_token`, `user`, `plans`, `discounts`, `trial_days`, `server_date`, `verification_gateway`
- If user unverified: returns `needs_verification: true` with token

### Frontend: `LoginPage.svelte`
- Form: email + password
- On success:
  - If `needs_verification` → simpan token di `pendingToken` (localStorage `lk_pending_token`), tampilkan verifikasi inline
  - Jika tidak → `token.set()` → `applyServerData()` → `onsuccess()`

### Verification Flow
- `POST /api/send-verification` → kirim kode via gateway (email/whatsapp/telegram)
- `POST /api/verify` → verifikasi kode 6 digit
- Timer 10 menit, cooldown 60 detik antar kirim
- Gateway dari `GET /api/config` → `verification_gateway`
- State disimpan di localStorage: `lk_pending_token`, `lk_verify_gateway`, `lk_verify_code_sent`

---

## 2. Register Flow

### Backend: `POST /api/register`
- **Controller**: `AuthController::register()`
- Fields: `name`, `email`, `phone`, `password`, `password_confirmation`, `ref`
- Validasi:
  - `email.unique` → "Email sudah digunakan"
  - `password.min` → "Password minimal 6 karakter"
  - `password.confirmed` → "Konfirmasi password tidak cocok"
- Returns: `access_token`, `user`, `needs_verification`, `verification_gateway`

### Frontend
- Form: nama, email, phone, password + konfirmasi (2 kolom), referral code
- Jika `needs_verification` → langsung ke verifikasi inline (tidak ke dashboard)

---

## 3. Forgot Password Flow

### Backend: `POST /api/forgot-password`
- **Controller**: `AuthController::forgotPassword()`
- Accepts `email` atau `phone`
- Gateway dari config: `FORGOT_PASSWORD_GATEWAY` (whatsapp/email/telegram)
- Strategy pattern: `NotificationChannelFactory::make($channel)->send()`
- WhatsApp → `wa.me` link, Email → Mail, Telegram → Bot API

### Frontend
- `openForgotPassword()` → fetch `GET /api/config` → baca `forgot_gateway`
- If `whatsapp` → input No. HP, If `email` → input Email
- Response: message saja (backend yang kirim)

---

## 4. After Login → Dashboard Load

### `+page.svelte` → `onMount()`
1. `auth.isAuthenticated` check
2. `api.getMe()` → `applyServerData(me)`
3. If `needs_verification` → show `VerificationPage` overlay
4. `seedAndLoad()`:
   - `anakStore.loadAnakList()` → `GET /api/anak`
   - `toolsStore.loadToolsData()` → load tools per anak
   - `activityStore.loadFromCache()` → build aktivitas data
   - `api.getPilarsAndSkills()` → `GET /api/pilars` → store ke `authStore.pilars` dan `authStore.skills`

### Auth Store (`authStore.js`)
Stores: `user`, `token`, `plans`, `userPlan`, `userRole`, `trialDays`, `serverDate`, `pilars`, `skills`, `needsVerification`, `verificationGateway`

### Anak Store (`anakStore.js`)
- `anakList` → writable store, sync ke localStorage
- `addAnak()`, `updateAnak()`, `deleteAnak()` → API + local

---

## 5. Bottom Navigation Tabs

| Tab | Component | Anak Selector | Filter |
|-----|-----------|---------------|--------|
| pilar (home) | `PilarTab` | `AnakDropdown` → `selectedAnakId` | agama, tahun, plan, search |
| activity | `ActivityTab` | `AnakDropdown` → `selectedAnakId` | agama, tahun, search |
| progress | `ProgressTab` | `AnakDropdown` → `toolsAnakId` | per anak |
| challenge | `ChallengeTab` | `AnakDropdown` → `toolsAnakId` | per anak |
| jadwal | `JadwalTab` | `AnakDropdown` → `toolsAnakId` | per anak |
| checklist | `ChecklistTab` | `AnakDropdown` → `toolsAnakId` | per anak |
| profile | `ProfileTab` | - | - |
| settings | `SettingsTab` | - | - |
| billing | `BillingTab` | - | - |
| referral | `ReferralTab` | - | - |

---

## 6. Home (PilarTab) - Filter Details

### Data Sources
- **Pilars**: `GET /api/pilars` → `authStore.pilars` → `getPilars()` with fallback
- **Skills**: `GET /api/pilars` → `authStore.skills` → `getSkills()` with fallback

### Filter Chain
```
selectedAnakId → selectedChild → childAge, childAgama, planId
    ↓
filterPilars(childAge, childAgama, planId)
    - ageOk: pilar.ages.includes(childAge)
    - agamaOk: pilar.agama.includes(childAgama) [server field: pilar_agama]
    - planOk: pilar.plans.includes(planId)
    ↓
filteredPilars + searchQuery filter (title, subtitle)
    ↓
getSkillsByPilar(key, childAge, childAgama, planId)
    - filterSkills({ pilarKey, childAge, childAgama, planId })
    - pilarOk: skill.pilars.includes(pilarKey)
    - ageOk: skill.ages.includes(childAge)
    - agamaOk: skill.agama.includes(childAgama) [server field: skill_agama]
    - planOk: skill.plans.includes(planId)
```

### Backend Models
- `Pilar`: `pilar_ages` (json), `pilar_agama` (json), `pilar_plans` (json)
- `MasterSkill`: `skill_ages` (json), `skill_pilars` (json), `skill_agama` (json), `skill_plans` (json)

---

## 7. Activity (ActivityTab) - Filter Details

### Data Sources
- `GET /api/activities?grouped` → grouped by type → `buildAktivitasDataFromAPI()`
- `aktivitasData` store → 11 types from `defaultMeta`

### defaultMeta (11 types, always visible)
| Key | Feature |
|-----|---------|
| storytelling | story |
| bermain_peran | roleplay |
| permainan | game |
| monolog | monolog |
| proyek_kreatif | project |
| musik_gerak | music |
| puzzle | puzzle |
| mindfulness | exercise |
| outdoor | activity |
| ilmu_pengetahuan | experiment |
| worksheet | worksheet |

### Filter Chain
```
aktData (reactive store subscription)
    ↓
filteredAktData = $derived.by(() => {
    1. Age/Agama filter per item:
       - ageOk: item.ages.includes(childAge)
       - agamaOk: item.agama.includes(childAgama)
    2. Search filter:
       - Match type title/desc → show all items in type
       - Match item title/desc → show matching items
    3. Semua 11 type SELALU tampil (tidak di-remove walau 0 items)
})
```

### Backend Model
- `Activity`: `ages` (json), `skills` (json), `plans` (json), `agama` (json)

### normalizeItem()
```js
{
    title, image, desc, moral,
    ages: item.ages || [],
    skills: item.skills || [],
    agama: item.agama || [],
    plans: item.plans || [],
    // + content based on type (pages, roles, games, etc.)
}
```

---

## 8. Progress (ProgressTab) - Filter Details

### Filter
- `AnakDropdown` → `toolsAnakId`
- `filteredAnakList = toolsAnakIdVal ? anakListVal.filter(a => a.id === toolsAnakIdVal) : anakListVal`
- Each anak card expandable → shows skills, evaluations

---

## 9. Challenge / Jadwal / Checklist

### Filter
- `AnakDropdown` → `toolsAnakId`
- Data filtered by selected anak

---

## 10. Profile (ProfileTab)

### Features
- Profile card: name, email, phone, gender, agama
- Edit profile modal → `PUT /api/profile`
- Ganti password modal → `PUT /api/password`
- Anak list → click to view (read-only after save)
- Tambah anak → confirm modal sebelum simpan
- Upgrade popup jika kuota habis

### Anak Data (tidak bisa diubah setelah simpan)
- Nama, Gender, Agama, Tanggal Lahir, Emoji
- Confirmation: "Pastikan data anak sudah benar karena data ini tidak dapat diubah di kemudian hari."

---

## 11. Billing (BillingTab)

### Features
- Current plan status (trial/expired/active)
- Plan list dari `authStore.plans`
- Checkout → `POST /api/payments` → QRIS
- Payment polling setiap 3 detik
- Discount code validation
- Payment history

### Guard
- `handleTrialGuard` di `+page.svelte`:
  - `noSubscribe` → redirect ke billing (kecuali sudah di billing)
  - `noAnak` → redirect ke profile (kecuali sudah di profile/billing)

---

## 12. Verification Flow (Cross-cutting)

### Backend
- `POST /api/send-verification` → kirim kode, cooldown 60 detik
- `POST /api/verify` → verifikasi kode, set `verified_at`
- `VerifyVerified` middleware → 403 jika belum verified
- `GET /api/me` → return `needs_verification: true` jika belum verified

### Frontend
- `LoginPage.svelte`: inline verification (token di `pendingToken`, tidak di store)
- `VerificationPage.svelte`: overlay di `+page.svelte` (untuk refresh case)
- `api.js`: 403 handler → trigger `onVerificationRequired` callback
- State: `lk_pending_token`, `lk_verify_gateway`, `lk_verify_code_sent`

---

## 13. Notification Strategy Pattern

### Backend
```
app/Services/Notification/
├── ChannelInterface.php       # send(to, message): bool
├── EmailChannel.php           # Mail::raw
├── WhatsAppChannel.php        # Meta API / Log::info
├── TelegramChannel.php        # Bot API / Log::info
└── NotificationChannelFactory.php  # make(channel)
```

### Config
```env
VERIFICATION_GATEWAY=email          # email/whatsapp/telegram
FORGOT_PASSWORD_GATEWAY=whatsapp    # email/whatsapp/telegram
WHATSAPP_TOKEN=xxx
WHATSAPP_PHONE_ID=xxx
TELEGRAM_BOT_TOKEN=xxx
TELEGRAM_CHAT_ID=xxx
```

---

## 14. API Endpoints Summary

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/login` | No | Login |
| POST | `/register` | No | Register |
| POST | `/forgot-password` | No | Reset password |
| POST | `/reset-password` | No | Apply reset token |
| GET | `/config` | No | Public config |
| GET | `/me` | Yes | Current user |
| PUT | `/profile` | Yes | Update profile |
| PUT | `/password` | Yes | Change password |
| POST | `/logout` | Yes | Logout |
| POST | `/send-verification` | Yes | Send code |
| POST | `/verify` | Yes | Verify code |
| GET | `/anak` | Yes+Verified | List anak |
| POST | `/anak` | Yes+Verified | Add anak |
| PUT | `/anak/{id}` | Yes+Verified | Update anak |
| DELETE | `/anak/{id}` | Yes+Verified | Delete anak |
| GET | `/pilars` | No | Pilars + Skills |
| GET | `/activities` | No | Activities |
| GET | `/plans` | No | Plans list |
| POST | `/payments` | Yes | Create payment |
| GET | `/payments` | Yes | Payment history |
| GET | `/payments/{id}` | Yes | Payment status |
| POST | `/payments/{id}/cancel` | Yes | Cancel payment |

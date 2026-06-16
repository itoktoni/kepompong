# Affiliate & Discount System

## Config (`backend/config/langkahkecil.php`)

```php
'affiliate' => [
    'register_bonus' => 500,              // Bonus saat referral daftar
    'upgrade_commission_rate' => 15,      // % komisi dari plan_harga
    'max_discounts' => 3,                 // Max kode diskon per affiliate
    'max_discount_value' => 15,           // Max % diskon yang bisa dibuat
    'max_discount_nominal' => 10000,      // Max nominal diskon fixed
],
```

Override via `.env`:
```
AFFILIATE_REGISTER_BONUS=500
AFFILIATE_UPGRADE_COMMISSION_RATE=15
AFFILIATE_MAX_DISCOUNTS=3
AFFILIATE_MAX_DISCOUNT_VALUE=15
AFFILIATE_MAX_DISCOUNT_NOMINAL=10000
```

---

## Komisi Calculation

Komisi dihitung real-time dari tabel `affiliate`, tidak disimpan di field user.

```
komisi = SUM(affiliate_jumlah WHERE status != 'rejected')
       - SUM(cashout_jumlah + cashout_admin_fee WHERE status IN ('pending','completed'))
```

**File:** `backend/app/Models/User.php` → `komisi()` method

---

## Flow: Register → Affiliate Bonus

```
User daftar dengan ?ref=XXXXXX
  └─ AuthController@register
      ├─ Cari referrer: User::where('affiliate_code', $ref)
      ├─ Create Affiliate record:
      │   ├─ affiliate_tipe = 'register'
      │   ├─ affiliate_jumlah = register_bonus (500)
      │   └─ affiliate_status = 'pending'
      └─ user.affiliate_reff = referrer code
```

**File:** `backend/app/Http/AuthController.php:191-206`

---

## Flow: Payment → Commission

```
Customer buat payment
  └─ PaymentController@create
      ├─ Ada discount_code? → lookup dari tabel discounts
      │   ├─ Hitung diskon (percentage atau fixed)
      │   └─ Simpan payment_diskon_code di payment
      └─ Tidak ada discount → harga normal

Customer bayar (settle)
  └─ PaymentController@settle
      ├─ payment_status = 'paid'
      └─ Dispatch ProcessPaidPayment

ProcessPaidPayment@handle
  ├─ Create Subscribe baru
  ├─ Update user.plan + user.role
  └─ processAffiliateUpgrade()
      ├─ Cek user.affiliate_reff ada?
      ├─ Ada payment_diskon_code?
      │   ├─ Lookup discount creator dari tabel discounts
      │   ├─ discount_created_by = referrer? → usedCustomCode = true
      │   └─ discountValue = percentage atau (fixed / plan_harga * 100)
      │
      ├─ Komisi = plan_harga * commission_rate / 100
      │   ├─ Tanpa custom code: full commission_rate (15%)
      │   └─ Dengan custom code: commission_rate - discount_value
      │       Contoh: 15% - 2% = 13%
      │
      ├─ Create Affiliate record:
      │   ├─ affiliate_tipe = 'upgrade'
      │   ├─ affiliate_jumlah = komisi
      │   ├─ affiliate_commission_rate = rate yang dipakai
      │   └─ affiliate_status = 'pending'
      └─ Log affiliate switch (jika referral pindah)
```

**File:** `backend/app/Jobs/ProcessPaidPayment.php:90-163`

---

## Flow: Discount Code → Affiliate Switch

Ketika customer pakai discount code dari affiliate B (berbeda dari referrer A):

```
ProcessPaidPayment@processAffiliateUpgrade
  ├─ Lookup discount → discount_created_by = B
  ├─ B punya affiliate_code?
  │   ├─ Ya → referrer = B (bukan A)
  │   ├─ Update user.affiliate_reff = B.code
  │   └─ Log: AffiliateSwitch from A to B
  └─ Komisi masuk ke B, bukan A

Catatan: Transaksi lama tetap di affiliate A.
Perubahan hanya berlaku untuk upgrade yang baru.
```

---

## Perhitungan Contoh

### Contoh 1: Tanpa Discount Code

```
Plan: Premium Rp159.000
Commission rate: 15%

Komisi = 159.000 × 15% = Rp23.850
```

### Contoh 2: Dengan Discount Code (2%)

```
Plan: Premium Rp159.000
Commission rate: 15%
Discount value: 2%

Effective rate = 15% - 2% = 13%
Komisi = 159.000 × 13% = Rp20.670
Customer dapat diskon = 159.000 × 2% = Rp3.180
```

### Contoh 3: Discount Code Fixed (Rp10.000)

```
Plan: Premium Rp159.000
Commission rate: 15%
Discount value: Rp10.000 (fixed)

Discount % = 10.000 / 159.000 × 100 = 6.29% → dibulatkan ke 6%
Effective rate = 15% - 6% = 9%
Komisi = 159.000 × 9% = Rp14.310
```

---

## Discount Code Management

### Buat Discount Code (Affiliate)

```
POST /api/discounts
  ├─ discount_code: string (uppercase, unique, 4-20 chars)
  ├─ discount_nama: string
  ├─ discount_type: 'percentage' | 'fixed'
  └─ discount_value: number (max: commission_rate untuk percentage)

Response: discount object
```

**File:** `backend/app/Http/DiscountController.php:31-71`

### Validate Discount Code (Customer)

```
POST /api/payments/validate-discount
  ├─ code: string
  └─ plan_id: integer

Response:
  ├─ valid: true/false
  ├─ code, name, type, rate
  └─ amount (diskon dalam Rupiah)
```

**File:** `backend/app/Http/PaymentController.php:169-224`

### Limit Discount Code

| Config | Default | Fungsi |
|--------|---------|--------|
| `max_discounts` | 3 | Max kode per affiliate |
| `max_discount_value` | 15% | Max percentage value |
| `max_discount_nominal` | Rp10.000 | Max fixed value |

---

## Cashout

```
POST /api/cashout
  ├─ amount: integer (min: cashout.minimum = 50000)
  └─ Validasi:
      ├─ komisi() >= amount + admin_fee
      ├─ rekening_nama, rekening_bank, rekening_nomor ada
      └─ Create Cashout record (status: pending)

Admin fee = amount × cashout.admin_rate / 100 (default 3%)
```

**File:** `backend/app/Http/AuthController.php:364-409`

---

## Tabel Terkait

| Tabel | Fungsi |
|-------|--------|
| `users` | affiliate_code, affiliate_reff, rekening_* |
| `affiliate` | Semua record komisi (register + upgrade) |
| `discounts` | Kode diskon yang dibuat affiliate |
| `payments` | payment_diskon_code menyimpan kode yang dipakai |
| `cashouts` | Record pencairan komisi |
| `subscribe` | Subscription user (plan, value, periode) |
| `plan` | Plan data (harga, value, periode) |

---

## Key Files

| File | Fungsi |
|------|--------|
| `backend/config/langkahkecil.php` | Config affiliate & cashout |
| `backend/app/Models/User.php` | `komisi()` method |
| `backend/app/Models/Affiliate.php` | Affiliate model |
| `backend/app/Models/Discount.php` | Discount model |
| `backend/app/Http/AuthController.php` | Register, me, affiliate code, cashout |
| `backend/app/Http/DiscountController.php` | Discount CRUD |
| `backend/app/Http/PaymentController.php` | Payment, validate discount |
| `backend/app/Jobs/ProcessPaidPayment.php` | Commission calculation |

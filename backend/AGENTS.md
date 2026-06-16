# AGENTS.md - Panduan untuk AI Agent

## Model Convention

Semua model extends `BaseModel` dan memiliki property lengkap.

### Struktur Model

```php
<?php

namespace App\Models;

use App\Models\BaseModel;

class Example extends BaseModel
{
    protected $table = 'examples';
    protected $keyType = 'int';
    protected $primaryKey = 'example_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'example_id' => 'Id',
        'example_nama' => 'Nama',
    ];

    public static $sortColumns = [
        'example_id',
        'example_nama',
    ];

    protected $fillable = [
        'example_id_user',
        'example_nama',
    ];

    protected $casts = [
        'example_active' => 'boolean',
    ];

    public function rules(): array
    {
        return [
            'example_nama' => 'required|string|max:255',
        ];
    }

    public function toArray(){}

    public static function field_name()
    {
        return 'example_nama';
    }

    public function has_user()
    {
        return $this->belongsTo(User::class, 'example_id_user');
    }

    public function has_items()
    {
        return $this->hasMany(Item::class, 'item_id_example');
    }
}
```

### Relationship Convention

Semua relationship menggunakan prefix `has_`:

| Type | Method Name | Example |
|------|-------------|---------|
| hasMany | `has_{plural}` | `has_skills()`, `has_items()` |
| belongsTo | `has_{singular}` | `has_user()`, `has_anak()`, `has_plan()` |

```php
// hasMany
public function has_skills()
{
    return $this->hasMany(Skill::class, 'skill_id_anak');
}

// belongsTo
public function has_anak()
{
    return $this->belongsTo(Anak::class, 'skill_id_anak', 'anak_id');
}
```

---

## Controller Convention

Setiap model punya controller dengan `ControllerTrait`.

### Struktur Controller

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Example;

class ExampleController extends Controller
{
    use ControllerTrait;

    public function __construct(Example $model)
    {
        $this->model = $model::getModel();
    }
}
```

### Controller dengan Custom Logic

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Example;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    use ControllerTrait;

    public function __construct(Example $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'options' => Example::getOptions(),
        ];

        return array_merge($default, $data);
    }
}
```

---

## Policy Convention

Setiap model punya policy extends `BasePolicy`.

### Struktur Policy

```php
<?php

namespace App\Policies;

class ExamplePolicy extends BasePolicy
{
}
```

`BasePolicy` menyediakan: `save`, `create`, `update`, `table`, `delete`, `show` permission checks via `config('permision')`.

---

## Route Convention

Routes didefinisikan di `routes/web.php` menggunakan [izniburak/laravel-auto-routes](https://github.com/izniburak/laravel-auto-routes).

### Dasar

```php
Route::auto('/example', 'ExampleController', ['name' => 'example']);
```

Semua method `public` di controller otomatis menjadi endpoint. URL dikonversi dari `camelCase` ke `kebab-case`.

### HTTP Method Prefix

Tambah prefix pada nama method untuk menentukan HTTP method:

| Prefix | HTTP Method | Contoh Method | URL |
|--------|-------------|---------------|-----|
| `get` | GET | `getFooBar()` | `/example/foo-bar` |
| `post` | POST | `postFooBar()` | `/example/foo-bar` |
| `put` | PUT | `putFooBar()` | `/example/foo-bar` |
| `delete` | DELETE | `deleteFooBar()` | `/example/foo-bar` |
| *(tanpa)* | ANY | `fooBar()` | `/example/foo-bar` |

### AJAX / Frontend Prefix (XMLHttpRequest)

Untuk endpoint yang dipanggil dari frontend (Svelte/fetch), gunakan prefix `x` di depan HTTP method:

| Prefix | HTTP Method | Contoh Method | URL |
|--------|-------------|---------------|-----|
| `xget` | GET + XHR | `xgetFoo()` | `/example/foo` |
| `xpost` | POST + XHR | `xpostBar()` | `/example/bar` |
| `xput` | PUT + XHR | `xputBaz()` | `/example/baz` |
| `xdelete` | DELETE + XHR | `xdeleteQux()` | `/example/qux` |
| `xany` | ANY + XHR | `xanyQuux()` | `/example/quux` |

Prefix `x` otomatis menambah middleware yang memastikan request hanya bisa diakses via XMLHttpRequest (fetch/AJAX). Jika diakses dari browser biasa, akan throw `MethodNotAllowedException`.

### Controller untuk Web (Blade)

```php
class ExampleController extends Controller
{
    use ControllerTrait;

    // GET /example/table — tampilkan halaman table
    public function getTable(GeneralRequest $request) { ... }

    // GET /example/create — tampilkan form create
    public function getCreate(GeneralRequest $request) { ... }

    // POST /example/create — proses form create
    public function postCreate(GeneralRequest $request) { ... }

    // GET /example/update/{id} — tampilkan form update
    public function getUpdate(GeneralRequest $request, $id) { ... }

    // POST /example/update/{id} — proses form update
    public function postUpdate(GeneralRequest $request, $id) { ... }

    // GET /example/delete/{id} — hapus data
    public function getDelete(GeneralRequest $request, $id) { ... }

    // POST /example/delete — bulk delete
    public function postDelete(GeneralRequest $request) { ... }
}
```

### Controller untuk Frontend API (Svelte/fetch)

```php
class ExampleController extends Controller
{
    // GET /example/items — ambil semua data
    public function xgetItems(Request $request) { ... }

    // POST /example/item — buat data baru
    public function xpostItem(Request $request) { ... }

    // PUT /example/item/{id} — update data
    public function xputItem(Request $request, $id) { ... }

    // DELETE /example/item/{id} — hapus data
    public function xdeleteItem(Request $request, $id) { ... }
}
```

### Options

```php
Route::auto('/example', 'ExampleController', [
    'name' => 'example',
    'middleware' => ['auth'],
    'patterns' => ['id' => '\\d+'],
    'only' => ['getTable', 'postCreate'],       // hanya generate method ini
    'except' => ['getDelete', 'postDelete'],     // exclude method ini
]);
```

### Parameter

```php
// URL: /example/{id} — parameter wajib
public function getShow(Request $request, int $id) { ... }

// URL: /example/{name}/{page?} — parameter optional
public function xgetItems(Request $request, string $name, int $page = 1) { ... }
```

### Route Manual di `routes/api.php`

Untuk endpoint API yang tidak menggunakan auto-route (misalnya perlu path berbeda), definisikan manual:

```php
Route::get('/activities', [ActivityController::class, 'index']);
Route::put('/activities/{id}', [ActivityController::class, 'update']);
```

---

## View Convention

Setiap controller punya folder views di `resources/views/pages/{module}/`.

### Struktur Views

```
resources/views/pages/example/
├── table.blade.php
└── form.blade.php
```

### table.blade.php

```blade
<?php /** @var App\Models\Example $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => ucfirst(module())]]" />
    <div class="content mt-4 lg:mt-0">
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @foreach ($fields as $key => $advance)
                <x-filter-item :label="$advance" :name="$key"/>
                @endforeach
                <x-button variant="primary" class="btn-block" onclick="applyAdvanced()">Apply</x-button>
                <x-button variant="soft" class="btn-block" onclick="resetAdvanced()">Reset</x-button>
            </x-slot:advanced>
        </x-filter>

        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    @foreach ($model::$sortColumns as $column)
                    <td>{{ $table->$column }}</td>
                    @endforeach
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <x-table-mobile-list>
                    @foreach($data as $table)
                    <x-table-mobile-item :id="$table->field_primary">
                        <x-table-mobile-header title="{{ $table->field_name }}" />
                        @foreach ($model::$sortColumns as $column)
                        <x-table-mobile-text :text="$table->$column" size="sm" color="primary" />
                        @endforeach
                        <x-table-mobile-footer :label="$table->field_primary">
                            <x-table-action :model="$model" :id="$table->field_primary" />
                        </x-table-mobile-footer>
                    </x-table-mobile-item>
                    @endforeach
                </x-table-mobile-list>
            </x-slot:mobile>
        </x-table>

        <x-pagination :paginator="$data" />
        <x-action :model="$model" :action="['create', 'delete']"/>
    </div>

    <input type="hidden" class="module" value="{{ module() }}">
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
</x-layouts::app>
```

### form.blade.php

```blade
<?php /** @var App\Models\Example $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                <x-input col="4" name="example_nama" />
                <x-input col="4" name="example_status" />
                <x-textarea col="12" name="example_catatan" />
            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
```

---

## Form dengan Fixed Bottom Action Bar

Saat membuat form yang memiliki tombol aksi (save, submit, dll) yang perlu selalu terlihat di bawah layar, gunakan component `<x-action>`.

### Struktur dengan Model (CRUD)

```blade
<x-form :model="$model" :action="route('...')" method="POST">
    <x-card :label="Title">
        @bind($model)
            {{-- Input fields --}}
        @endbind
    </x-card>

    <x-action :model="$model" :action="['save']"/>
</x-form>
```

### Struktur Tanpa Model (Manual)

Untuk form tanpa model (seperti settings/env), buat manual karena `<x-action>` menggunakan `@can` check yang memerlukan model:

```blade
<form action="{{ route('...') }}" method="POST">
    @csrf
    {{-- Form content --}}

    <style>
        @media (min-width: 768px) { .action-bar { bottom: 0 !important; } }
    </style>
    <div class="action-bar fixed left-0 right-0 lg:left-72 bg-surface-container-lowest border-t border-outline-variant shadow-[0_-4px_12px_rgba(0,0,0,0.08)] px-4 md:px-6 py-3 z-[45]" style="bottom: 4rem">
        <div class="flex items-center justify-end max-w-full mx-auto gap-3">
            <x-button type="submit" icon="save">Save</x-button>
        </div>
    </div>
    <div class="h-28 md:h-16"></div>
</form>
```

### Cara Kerja

| Behavior | Mobile | Desktop |
|----------|--------|---------|
| Posisi | `style="bottom: 4rem"` (di atas bottom nav `h-16`) | CSS media query `bottom: 0 !important` |
| Sidebar offset | Tidak ada | `lg:left-72` |
| Z-index | `z-[45]` | `z-[45]` |
| Spacer | `h-28` (7rem) | `h-16` (4rem) |

### Catatan Penting

- `<x-action>` menggunakan `@can('save', $model)` — hanya gunakan untuk form dengan model yang punya policy
- Untuk form tanpa model, buat manual HTML dengan class yang sama seperti di atas
- Jangan lupa `<div class="h-28 md:h-16"></div>` sebagai spacer setelah action bar

---

## Menu Configuration

Menu didefinisikan di `config/menu.php` dan di-render oleh component Blade.

### Config Structure (`config/menu.php`)

```php
return [
    'sidebar' => [
        [
            'label' => null,
            'items' => [
                ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                ['route' => 'user.getTable', 'icon' => 'manage_accounts', 'label' => 'Users'],
            ],
        ],
        [
            'label' => 'Settings',
            'items' => [
                ['route' => 'profile.edit', 'icon' => 'person', 'label' => 'My Profile'],
                ['route' => 'settings.env', 'icon' => 'settings', 'label' => 'Environment'],
            ],
        ],
    ],
    'bottom_nav' => [
        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Home'],
        ['route' => 'profile.edit', 'icon' => 'person', 'label' => 'Profile'],
    ],
];
```

### Menambah Menu Baru

1. Buka `config/menu.php`
2. Tambah item di section yang sesuai:
   - `sidebar` — untuk desktop sidebar dan mobile drawer
   - `bottom_nav` — untuk bottom nav mobile (max 5 items)
3. Format: `['route' => 'route.name', 'icon' => 'material_icon', 'label' => 'Display Label']`

---

## Database Naming Convention

### Bahasa Indonesia

Semua nama field menggunakan **bahasa Indonesia**. Contoh:

| English | Indonesia |
|---------|-----------|
| name | nama |
| note | catatan |
| amount | jumlah |
| status | status |
| type | tipe |
| description | keterangan |
| price | harga |
| date | tanggal |
| image | gambar |
| address | alamat |
| phone | telepon |

### Table Naming

Table names use **plural snake_case** (default Laravel convention):

```
users, payments, affiliate, cashouts, plans
```

Exception: entity-specific tables can use singular if it represents a domain concept (e.g., `affiliate`).

### Field Naming

All fields use prefix `{table_singular}_` with the table name as prefix.

#### Rules

1. **Primary key**: `{table}_id` (e.g., `payment_id`, `affiliate_id`, `cashout_id`)
2. **Foreign keys**: `{table}_id_{reference}` (e.g., `payment_id_user`, `affiliate_id_from_user`, `cashout_id_user`)
3. **Regular fields**: `{table}_{field_indonesia}` (e.g., `payment_jumlah`, `payment_status`, `affiliate_tipe`)
4. **Timestamps**: `{table}_created_at`, `{table}_updated_at` (e.g., `payment_created_at`)

#### Examples

| Table | PK | Foreign Keys | Fields |
|-------|-----|-------------|--------|
| `users` | `id` | - | `nama`, `email`, `role`, `affiliate_code`, `affiliate_reff`, `rekening_nama`, `rekening_bank`, `rekening_nomor` |
| `payments` | `payment_id` | `payment_id_user`, `payment_id_plan` | `payment_order_code`, `payment_jumlah`, `payment_diskon`, `payment_total`, `payment_qris_string`, `payment_status`, `payment_metode`, `payment_paid_at`, `payment_expired_at`, `payment_created_at`, `payment_updated_at` |
| `affiliate` | `affiliate_id` | `affiliate_id_user`, `affiliate_id_from_user`, `affiliate_id_payment` | `affiliate_tipe`, `affiliate_jumlah`, `affiliate_payment_jumlah`, `affiliate_commission_rate`, `affiliate_catatan`, `affiliate_status`, `affiliate_created_at`, `affiliate_updated_at` |
| `cashouts` | `cashout_id` | `cashout_id_user` | `cashout_jumlah`, `cashout_admin_fee`, `cashout_diterima`, `cashout_rekening_bank`, `cashout_rekening_nomor`, `cashout_rekening_nama`, `cashout_status`, `cashout_catatan`, `cashout_created_at`, `cashout_updated_at` |

#### Creating New Table

When creating a new table, follow this template (gunakan bahasa Indonesia untuk nama field):

```php
Schema::create('examples', function (Blueprint $table) {
    $table->id('example_id');
    $table->integer('example_id_user');
    $table->string('example_nama');
    $table->integer('example_jumlah');
    $table->enum('example_status', ['aktif', 'nonaktif'])->default('aktif');
    $table->text('example_catatan')->nullable();
    $table->dateTime('example_created_at')->nullable();
    $table->dateTime('example_updated_at')->nullable();
});
```

---

## Mobile Drawer Padding

Drawer sidebar di mobile perlu padding bottom agar menu di bagian bawah (seperti Settings) bisa di-scroll dan terlihat.

Tambahkan `pb-24` pada `<nav>` di dalam mobile drawer:

```blade
<nav class="flex-1 py-4 px-3 pb-24 space-y-1 overflow-y-auto">
    <x-menu-items :mobile="true" />
</nav>
```

---

## Filter Component

Component `<x-filter>` digunakan untuk search, perpage, dan filter pada halaman table.

```blade
<x-filter :fields="$fields" searchPlaceholder="Search..." />
```

---

## Adding New Module Checklist

Saat menambahkan module baru, ikuti checklist ini:

1. **Model** — extends `BaseModel` dengan `$filterColumns`, `$sortColumns`, `$fillable`, `rules()`, `toArray(){}`, `field_name()`
2. **Policy** — extends `BasePolicy`
3. **Controller** — uses `ControllerTrait` dengan constructor `$model::getModel()`
4. **Route** — `Route::auto('/module', 'ModuleController', ['name' => 'module'])`
5. **Menu** — tambah di `config/menu.php`
6. **Views** — `pages/{module}/table.blade.php` + `pages/{module}/form.blade.php`
7. **Frontend API** — gunakan prefix `x` (`xget`, `xpost`, `xput`, `xdelete`) untuk endpoint yang dipanggil dari Svelte

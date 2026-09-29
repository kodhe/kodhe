# default_project — Contoh Proyek Kodhe Framework

Template struktur aplikasi sesuai panduan modul Kodhe Framework:
setiap fitur adalah **satu modul HMVC** di `application/modules/{nama}`.

## Contoh modul: `blog`

Modul lengkap dengan lapisan service + controller + view + rute sendiri:

```
application/
└── modules/
    ├── blog/                       ← MODUL contoh (lapisan service lengkap)
    │   ├── setup.php               ← pendaftaran service/singleton/model/alias
    │   ├── Services/               ← PostService, TagService, StatsService
    │   ├── Models/                 ← Post (model domain, deps disuntikkan)
    │   ├── config/routes.php       ← opsional: fallback rute gaya CI3
    │   ├── controllers/            ← Post.php, Admin.php (memakai service modul)
    │   ├── models/                 ← (kosong — hanya model CI-style lokal)
    │   ├── views/post/index.php    ← presentasi
    │   └── routes/                 ← modul memiliki folder routes sendiri
    │       ├── web.php             ← halaman HTML (Route::module('blog', …))
    │       ├── api.php             ← endpoint JSON (Route::apiVersion('v1', …))
    │       └── console.php         ← perintah CLI pemanggil service modul
    └── shop/                       ← modul minimal (hanya setup.php kosong-ish)
```

Dokumentasi lengkap: [`user_guide/id/libraries/module.md`](../user_guide/id/libraries/module.md)
(EN: `user_guide/en/libraries/module.md`).

## Mengaktifkan modul (urutan boot)

Di bootstrap aplikasi (mis. hook `Kernel::boot`), daftarkan lapisan service
SETIAP modul SEBELUM `Modules::init()` — urutan wajib:
`addProvider()` → `setClassAliases()` → `Modules::init()`:

```php
use Kodhe\Framework\Container\Container;
use Kodhe\Framework\Foundation\Service\{ServiceLocator, ServiceManager};
use Kodhe\Framework\Support\Autoloader;
use Kodhe\Framework\Support\Modules;

$container = kodhe('di');
$locator   = new ServiceLocator($container);
$manager   = new ServiceManager($container, $locator);
$manager->setAutoloader(new Autoloader());

// JANGAN pakai helper legacy setupAddons('addons') — tidak ada pohon addons/.
foreach (Modules::list_modules() as $module) {
    $manager->addProvider(APPPATH . 'modules/' . $module); // baca modules/{m}/setup.php
}

$manager->setClassAliases();
Modules::init();
```

## Memakai service modul

Prefix service mengikuti nama folder modul (`blog` → `blog:PostService`):

```php
service('PostService', 'blog')->all();          // gaya disarankan
kodhe('di')->make('blog:PostService')->all();   // container langsung
ServiceHelper::tag_service();                   // shorthand statis
```

## Menambah modul baru

Salin pola `modules/blog/`: buat folder per fitur, isi `controllers/`,
`views/`, dan `routes/web.php|api.php|console.php`. Tambahkan `setup.php`
+ `Services/` hanya bila modul butuh lapisan service. Modul tanpa
`setup.php` tetap boot normal.

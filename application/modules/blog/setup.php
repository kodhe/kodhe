<?php

declare(strict_types=1);

/**
 * Pendaftaran lapisan service milik modul "blog".
 *
 * File ini berada LANGSUNG di root modul (modules/blog/setup.php) — bukan di
 * folder "addons/" terpisah. ServiceManager membacanya lewat:
 *
 *     $manager->addProvider(APPPATH . 'modules/blog');   // prefix = "blog"
 *
 * Prefix service mengikuti nama folder modul: blog => "blog:PostService".
 */
return [
    'name'      => 'Blog',
    'version'   => '1.0.0',
    'author'    => 'Kodhe Team',
    'license'   => 'MIT',
    'namespace' => 'Modules\\Blog',          // didaftarkan ke Autoloader via addPrefix()

    // Layanan biasa (instance baru tiap resolusi) — [nama => Closure|string]
    'services' => [
        'PostService' => static function ($provider) {
            return new \Modules\Blog\Services\PostService();
        },
        // String = FQCN relatif terhadap namespace modul.
        'TagService'  => 'Services\TagService',
    ],

    // Layanan singleton (satu instance per proses).
    'services.singletons' => [
        'StatsService' => static fn ($provider) => new \Modules\Blog\Services\StatsService(),
    ],

    // Model domain yang dipakai semua bagian modul.
    'models' => [
        'Post' => 'Modules\\Blog\\Models\\Post',  // nama model => kelas riil
    ],

    // Dependensi silang yang disuntikkan provider saat model dibangun,
    // sehingga controller tidak pernah merangkai manual.
    'models.dependencies' => [
        'Post' => ['kodhe:db'],
    ],

    // Alias agar kode gaya lama (CI3) tetap jalan tanpa perubahan.
    'aliases' => [
        'CI_Post' => 'Modules\\Blog\\Models\\Post',
    ],
];

<?php

declare(strict_types=1);

/**
 * Modul "shop" — contoh modul TANPA lapisan service tambahan.
 *
 * Modul tanpa setup.php adalah modul HMVC biasa yang sepenuhnya normal.
 * File ini ada hanya sebagai penanda bahwa Anda BOLEH menambah lapisan
 * service per modul bila dibutuhkannya. Hapus file ini untuk kembali ke
 * modul HMVC polos.
 */
return [
    'name'      => 'Shop',
    'version'   => '0.1.0',
    'author'    => 'Kodhe Team',
    'namespace' => 'Modules\\Shop',
    'services'  => [
        // 'PaymentService' => static fn ($provider) => new \Modules\Shop\Services\PaymentService(),
    ],
];

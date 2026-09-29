<?php

declare(strict_types=1);

namespace Modules\Blog\Models;

/**
 * Model domain modul (dideklarasikan di 'models' setup.php => Modules\Blog\Post).
 *
 * Dependensi 'kodhe:db' disuntikkan provider lewat 'models.dependencies',
 * jadi controller tidak pernah merangkainya manual.
 */
class Post
{
    public function __construct(private readonly object $db)
    {
        // $db = instance "kodhe:db" hasil injeksi models.dependencies.
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        // Contoh integrasi database nyata:
        // return $this->db->table('posts')->get()->result_array();
        return [];
    }
}

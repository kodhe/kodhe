<?php

declare(strict_types=1);

namespace Modules\Blog\Services;

/**
 * Singleton modul: terdaftar di 'services.singletons' pada setup.php
 * dengan key "blog:StatsService". Bisa dipanggil lewat beberapa ejaan —
 * ServiceHelper menambahkan suffix "Service" dan mengubah snake_case:
 *
 *     service('stats', 'blog');              // auto-suffix + snake_case
 *     service('StatsService', 'blog');       // eksplisit
 *     ServiceHelper::stats_service();        // shorthand statis
 */
class StatsService
{
    public function countPosts(PostService $posts): int
    {
        return count($posts->all());
    }
}

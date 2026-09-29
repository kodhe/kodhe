<?php

declare(strict_types=1);

/**
 * Perintah CLI milik modul blog — memanggil service modul LANGSUNG,
 * tanpa lapisan HTTP. Dijalankan misalnya via: php kodhe blog:stats
 */

use Kodhe\Framework\Console\Console;

Console::command('blog:stats', static function (): void {
    $posts = service('PostService', 'blog');
    $stats = service('stats', 'blog');

    echo 'Total posting: ' . $stats->countPosts($posts) . PHP_EOL;

    foreach ($posts->all() as $post) {
        echo "- [{$post['id']}] {$post['title']}" . PHP_EOL;
    }
});

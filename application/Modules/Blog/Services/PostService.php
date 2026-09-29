<?php

declare(strict_types=1);

namespace Modules\Blog\Services;

/**
 * Logika bisnis modul blog (kepemilikan modul — jangan di-new dari modul lain).
 *
 * Di-resolve sebagai "blog:PostService" oleh container melalui setup.php.
 * Contoh pemakaian di dalam controller modul ini:
 *
 *     $posts = service('PostService', 'blog')->all();
 *     $posts = kodhe('di')->make('blog:PostService')->all();
 */
class PostService
{
    /** @var array<int, array{id:int,title:string,body:string}> */
    private array $posts = [
        ['id' => 1, 'title' => 'Hello world', 'body' => 'Posting pertama di modul blog.'],
        ['id' => 2, 'title' => 'Modul Kodhe', 'body' => 'Satu fitur = satu modul HMVC.'],
    ];

    /**
     * Semua posting.
     *
     * @return array<int, array{id:int,title:string,body:string}>
     */
    public function all(): array
    {
        return $this->posts;
    }

    /**
     * Posting terbaru sebanyak $limit (urut id menurun).
     *
     * @return array<int, array{id:int,title:string,body:string}>
     */
    public function latest(int $limit = 5): array
    {
        $sorted = $this->posts;
        usort($sorted, static fn (array $a, array $b): int => $b['id'] <=> $a['id']);

        return array_slice($sorted, 0, $limit);
    }

    public function find(int $id): ?array
    {
        foreach ($this->posts as $post) {
            if ($post['id'] === $id) {
                return $post;
            }
        }

        return null;
    }

    public function create(string $title, string $body): array
    {
        $post = [
            'id'    => count($this->posts) > 0 ? max(array_column($this->posts, 'id')) + 1 : 1,
            'title' => $title,
            'body'  => $body,
        ];

        $this->posts[] = $post;

        return $post;
    }
}

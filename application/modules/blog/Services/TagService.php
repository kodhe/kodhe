<?php

declare(strict_types=1);

namespace Modules\Blog\Services;

/**
 * Didaftarkan di setup.php sebagai string relatif namespace modul:
 * 'TagService' => 'Services\TagService'  =>  blog:TagService
 */
class TagService
{
    /** @var array<int, string> */
    private array $tags = ['kodhe', 'modul', 'hmvc'];

    /**
     * @return array<int, string>
     */
    public function all(): array
    {
        return $this->tags;
    }

    public function add(string $tag): void
    {
        if (!in_array($tag, $this->tags, true)) {
            $this->tags[] = $tag;
        }
    }
}

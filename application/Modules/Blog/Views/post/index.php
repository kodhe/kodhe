<?php

declare(strict_types=1);

/**
 * View daftar posting modul blog (modules/blog/views/post/index.php).
 * $posts, $stats, $tags dikirim dari Post::index().
 */
?>
<h1>Blog</h1>

<ul>
    <?php foreach ($posts as $post): ?>
        <li>
            <a href="<?= site_url('blog/post/' . $post['id']) ?>">
                <?= html_escape($post['title']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<p><small>Tag: <?= html_escape(implode(', ', $tags)) ?></small></p>

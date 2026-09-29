<?php

declare(strict_types=1);

/**
 * View detail satu posting modul blog (modules/blog/views/post/show.php).
 * Dipakai Post::show() — $post dikirim dari service('blog:PostService')->find($id).
 */
?>
<h1><?= html_escape($post['title']) ?></h1>

<p><?= nl2br(html_escape($post['body'])) ?></p>

<p><a href="<?= site_url('blog') ?>">&larr; Kembali ke daftar blog</a></p>

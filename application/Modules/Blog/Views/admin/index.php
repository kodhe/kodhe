<?php

declare(strict_types=1);

/**
 * View panel admin modul blog (modules/blog/views/admin/index.php).
 * Dipakai Admin::index() — $posts berisi 10 posting terbaru (latest(10)).
 */
?>
<h1>Blog Admin</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Judul</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= (int) $post['id'] ?></td>
                <td><?= html_escape($post['title']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

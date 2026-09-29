<?php

declare(strict_types=1);

/**
 * Fallback rute gaya CI3 untuk modul blog (opsional).
 * File modern routes/web.php|api.php|console.php lebih diprioritaskan;
 * entri ini tetap dilayani mesin legacy router.
 */

$route['blog/post/(:num)'] = 'blog/post/view/$1';

<?php

declare(strict_types=1);

namespace Modules\Blog\Controllers;

use Kodhe\Framework\Foundation\Service\ServiceHelper;

/**
 * Controller utama modul blog (HMVC: URL /blog/post/...).
 *
 * Base controller mengikuti konvensi aplikasi Anda — di panduan modul
 * dipakai \KM_Controller; ganti dengan CI_Controller / App\Core\Controller
 * bila proyek memakai basis lain.
 */
class Post extends \KM_Controller
{
    /**
     * Halaman depan blog: daftar posting + tag + statistik.
     */
    public function index()
    {
        // Gaya 1 (disarankan): helper service() dengan prefix eksplisit.
        $data['posts'] = service('PostService', 'blog')->all();

        // Auto-suffix "Service" + ejaan snake_case juga diterima.
        $data['stats'] = service('stats', 'blog');

        // Shorthand statis ServiceHelper.
        $data['tags'] = ServiceHelper::tag();

        $this->load->view('post/index', $data);
    }

    /**
     * Gaya 2: container langsung (DI eksplisit, mudah dites).
     */
    public function show(int $id)
    {
        $post = kodhe('di')->make('blog:PostService')->find($id);

        if ($post === null) {
            show_404();
        }

        $this->load->view('post/show', ['post' => $post]);
    }

    /**
     * Model domain modul — dependensi 'kodhe:db' sudah disuntikkan provider
     * lewat 'models.dependencies' di setup.php, jadi controller tidak
     * merangkainya manual.
     */
    public function fromDatabase()
    {
        $posts = service('Post', 'blog')->all();

        $this->load->view('post/index', ['posts' => $posts, 'stats' => null, 'tags' => []]);
    }
}

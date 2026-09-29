<?php

declare(strict_types=1);

namespace Modules\Blog\Controllers;

/**
 * Controller admin modul blog. Karena route modul di-group lewat
 * Route::module('blog', ...) + middleware 'auth', URL-nya: /blog/admin.
 *
 * Contoh gaya constructor injection — router membangun controller melalui
 * container sehingga PostService ikut ter-resolve otomatis.
 */
class Admin extends \KM_Controller
{
    public function __construct(private readonly object $posts)
    {
        parent::__construct();
    }

    public function index()
    {
        $data['posts'] = $this->posts->latest(10);
        $this->load->view('admin/index', $data);
    }
}

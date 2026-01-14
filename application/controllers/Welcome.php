<?php namespace App\Controllers;

class Welcome extends \CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Load language helper
        $this->load->helper('language');
        
        // Set language from session or default
        $lang = app('session')->userdata('language') ?? 'english';

        app('lang')->load('welcome', $lang);
    }

    public function index()
    {
        // Set language from GET parameter if provided
        $request_lang = $this->input->get('lang');
        /*if ($request_lang === 'id') {
            app('session')->set_userdata('language', 'indonesian');
            app('lang')->load('welcome', 'indonesian');
        } elseif ($request_lang === 'en') {
            app('session')->set_userdata('language', 'english');
            app('lang')->load('welcome', 'english');
        }*/
        
        // Get current language for view
        $current_lang = app('session')->userdata('language') ?? 'english';
        $lang_code = ($current_lang === 'indonesian') ? 'id' : 'en';
        
        // Prepare badges array
        $badges = [
            app('lang')->line('welcome_badge_mvc'),
            app('lang')->line('welcome_badge_lightweight'),
            app('lang')->line('welcome_badge_secure'),
            app('lang')->line('welcome_badge_powerful')
        ];
        
        // Prepare requirements list
        $requirements = [
            app('lang')->line('welcome_req_php'),
            app('lang')->line('welcome_req_db'),
            app('lang')->line('welcome_req_server'),
            app('lang')->line('welcome_req_composer')
        ];
        
        // Prepare features array
        $features = [
            [
                'title' => app('lang')->line('welcome_feature_mvc_title'),
                'description' => app('lang')->line('welcome_feature_mvc_desc'),
                'icon' => '🏗️'
            ],
            [
                'title' => app('lang')->line('welcome_feature_lightweight_title'),
                'description' => app('lang')->line('welcome_feature_lightweight_desc'),
                'icon' => '⚡'
            ],
            [
                'title' => app('lang')->line('welcome_feature_security_title'),
                'description' => app('lang')->line('welcome_feature_security_desc'),
                'icon' => '🛡️'
            ],
            [
                'title' => app('lang')->line('welcome_feature_easy_title'),
                'description' => app('lang')->line('welcome_feature_easy_desc'),
                'icon' => '📚'
            ],
            [
                'title' => app('lang')->line('welcome_feature_db_title'),
                'description' => app('lang')->line('welcome_feature_db_desc'),
                'icon' => '🗄️'
            ],
            [
                'title' => app('lang')->line('welcome_feature_rest_title'),
                'description' => app('lang')->line('welcome_feature_rest_desc'),
                'icon' => '🔌'
            ]
        ];
        
        // Prepare data for view
        $data = [
            'title' => app('lang')->line('welcome_title'),
            'lang' => $lang_code,
            'current_lang' => $current_lang,
            'framework_name' => app('lang')->line('welcome_framework_name'),
            'ci_version' => CI_VERSION,
            'tagline' => app('lang')->line('welcome_tagline'),
            'version_text' => sprintf(app('lang')->line('welcome_version'), CI_VERSION),
            'badges' => $badges,
            'requirements' => $requirements,
            'features' => $features,
            'environment' => ENVIRONMENT,
            'elapsed_time' => app('benchmark')->elapsed_time(),
            'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2),
            'current_year' => date('Y'),
            'file_path' => app('lang')->line('welcome_file_path'),
            'base_url' => base_url(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'php_version' => phpversion(),
            'timezone' => date_default_timezone_get()
        ];
        
        //return app('view')->render('welcome', $data);
        
        // Load view
        return $this->load->view('pages.welcome', $data);
    }
    
    /**
     * Switch language
     */
    public function switch_language($lang)
    {
        if ($lang === 'id') {
            app('session')->set_userdata('language', 'indonesian');
        } else {
            app('session')->set_userdata('language', 'english');
        }
        
        // Redirect back to welcome page
        redirect('welcome');
    }
}
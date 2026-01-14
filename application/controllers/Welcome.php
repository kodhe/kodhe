<?php namespace App\Controllers;

class Welcome extends \CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Load language helper
        $this->load->helper('language');
        
    }

    public function index()
    {

        // Get current language for view
        $current_lang = $this->session->userdata('language') ?? 'indonesian';

        $lang_code = ($current_lang === 'indonesian') ? 'id' : 'en';
        
        $this->lang->load('welcome', $current_lang);
        
        // Prepare badges array
        $badges = [
            $this->lang->line('welcome_badge_mvc'),
            $this->lang->line('welcome_badge_lightweight'),
            $this->lang->line('welcome_badge_secure'),
            $this->lang->line('welcome_badge_powerful')
        ];
        
        // Prepare requirements list
        $requirements = [
            $this->lang->line('welcome_req_php'),
            $this->lang->line('welcome_req_db'),
            $this->lang->line('welcome_req_server'),
            $this->lang->line('welcome_req_composer')
        ];
        
        // Prepare features array
        $features = [
            [
                'title' => $this->lang->line('welcome_feature_mvc_title'),
                'description' => $this->lang->line('welcome_feature_mvc_desc'),
                'icon' => '🏗️'
            ],
            [
                'title' => $this->lang->line('welcome_feature_lightweight_title'),
                'description' => $this->lang->line('welcome_feature_lightweight_desc'),
                'icon' => '⚡'
            ],
            [
                'title' => $this->lang->line('welcome_feature_security_title'),
                'description' => $this->lang->line('welcome_feature_security_desc'),
                'icon' => '🛡️'
            ],
            [
                'title' => $this->lang->line('welcome_feature_easy_title'),
                'description' => $this->lang->line('welcome_feature_easy_desc'),
                'icon' => '📚'
            ],
            [
                'title' => $this->lang->line('welcome_feature_db_title'),
                'description' => $this->lang->line('welcome_feature_db_desc'),
                'icon' => '🗄️'
            ],
            [
                'title' => $this->lang->line('welcome_feature_rest_title'),
                'description' => $this->lang->line('welcome_feature_rest_desc'),
                'icon' => '🔌'
            ]
        ];
        
        // Prepare data for view
        $data = [
            'title' => $this->lang->line('welcome_title'),
            'lang' => $lang_code,
            'current_lang' => $current_lang,
            'framework_name' => $this->lang->line('welcome_framework_name'),
            'ci_version' => CI_VERSION,
            'tagline' => $this->lang->line('welcome_tagline'),
            'version_text' => sprintf($this->lang->line('welcome_version'), CI_VERSION),
            'badges' => $badges,
            'requirements' => $requirements,
            'features' => $features,
            'environment' => ENVIRONMENT,
            'elapsed_time' => app('benchmark')->elapsed_time(),
            'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2),
            'current_year' => date('Y'),
            'file_path' => $this->lang->line('welcome_file_path'),
            'base_url' => base_url(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'php_version' => phpversion(),
            'timezone' => date_default_timezone_get()
        ];
                
        // Load view
        return $this->load->view('pages.welcome', $data);
    }
    
    /**
     * Switch language
     */
    public function switch_language($lang)
    {
        if ($lang === 'id') {
            $this->session->set_userdata('language', 'indonesian');
        } else {
            $this->session->set_userdata('language', 'english');
        }
        
        // Redirect back to welcome page
        redirect('welcome');
    }
}
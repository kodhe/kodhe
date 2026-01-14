<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * THEME HELPER FUNCTIONS
 * 
 * Helper functions untuk mempermudah penggunaan theme system
 * seperti PyroCMS 2 Style
 */

// ==============================================
// THEME INSTANCE & INFORMATION
// ==============================================

if (!function_exists('theme')) {
    /**
     * Get theme library instance
     * 
     * @return object Theme library instance
     */
    function theme()
    {
        $CI =& get_instance();
        if (!isset($CI->theme)) {
            $CI->load->library('theme');
        }
        return $CI->theme;
    }
}

if (!function_exists('theme_info')) {
    /**
     * Get current theme information
     * 
     * @param string|null $key Specific key dari theme info
     * @return mixed Theme info array atau specific value
     */
    function theme_info($key = null)
    {
        $theme = theme();
        $info = $theme->get_info();
        
        if ($key) {
            return isset($info[$key]) ? $info[$key] : null;
        }
        
        return $info;
    }
}

if (!function_exists('theme_name')) {
    /**
     * Get current active theme name
     * 
     * @return string Active theme name
     */
    function theme_name()
    {
        return theme()->get_active_theme();
    }
}

if (!function_exists('theme_path')) {
    /**
     * Get current theme path
     * 
     * @param string $subpath Subpath tambahan
     * @return string Full theme path
     */
    function theme_path($subpath = '')
    {
        $theme = theme();
        $path = $theme->path;
        
        if ($subpath) {
            $path .= ltrim($subpath, '/');
        }
        
        return $path;
    }
}

if (!function_exists('theme_web_path')) {
    /**
     * Get current theme web path (URL)
     * 
     * @param string $subpath Subpath tambahan
     * @return string Theme web path URL
     */
    function theme_web_path($subpath = '')
    {
        $theme = theme();
        $web_path = $theme->web_path;
        
        if ($subpath) {
            $web_path .= ltrim($subpath, '/');
        }
        
        return $web_path;
    }
}

// ==============================================
// THEME ASSETS FUNCTIONS
// ==============================================

if (!function_exists('theme_asset')) {
    /**
     * Get theme asset URL
     * 
     * @param string $path Asset path relatif ke folder assets theme
     * @param string|null $theme Theme name (optional, default current theme)
     * @return string Full asset URL
     */
    function theme_asset($path, $theme = null)
    {
        $CI =& get_instance();
        if (!isset($CI->theme_asset)) {
            $CI->load->library('theme_asset');
        }
        
        return $CI->theme_asset->url($path, $theme);
    }
}

if (!function_exists('theme_css')) {
    /**
     * Get theme CSS URL
     * 
     * @param string $file CSS filename
     * @param string|null $theme Theme name
     * @return string CSS URL
     */
    function theme_css($file, $theme = null)
    {
        return theme_asset('css/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('theme_js')) {
    /**
     * Get theme JS URL
     * 
     * @param string $file JS filename
     * @param string|null $theme Theme name
     * @return string JS URL
     */
    function theme_js($file, $theme = null)
    {
        return theme_asset('js/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('theme_img')) {
    /**
     * Get theme image URL
     * 
     * @param string $file Image filename
     * @param string|null $theme Theme name
     * @return string Image URL
     */
    function theme_img($file, $theme = null)
    {
        return theme_asset('img/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('theme_font')) {
    /**
     * Get theme font URL
     * 
     * @param string $file Font filename
     * @param string|null $theme Theme name
     * @return string Font URL
     */
    function theme_font($file, $theme = null)
    {
        return theme_asset('fonts/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('theme_upload')) {
    /**
     * Get theme upload URL
     * 
     * @param string $file Uploaded filename
     * @param string|null $theme Theme name
     * @return string Upload URL
     */
    function theme_upload($file, $theme = null)
    {
        return theme_asset('uploads/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('add_theme_css')) {
    /**
     * Add CSS file to theme
     * 
     * @param string $file CSS file path
     * @param string $group Group name (default: 'theme')
     * @param array $attributes HTML attributes
     * @param int $priority Loading priority
     * @return object Theme instance untuk chaining
     */
    function add_theme_css($file, $group = 'theme', $attributes = [], $priority = 10)
    {
        return theme()->add_css($file, $group, $attributes, $priority);
    }
}

if (!function_exists('add_theme_js')) {
    /**
     * Add JS file to theme
     * 
     * @param string $file JS file path
     * @param string $group Group name (default: 'theme')
     * @param array $attributes HTML attributes
     * @param string $position 'header' atau 'footer'
     * @param int $priority Loading priority
     * @return object Theme instance untuk chaining
     */
    function add_theme_js($file, $group = 'theme', $attributes = [], $position = 'footer', $priority = 10)
    {
        return theme()->add_js($file, $group, $attributes, $position, $priority);
    }
}

if (!function_exists('add_inline_css')) {
    /**
     * Add inline CSS
     * 
     * @param string $css CSS code
     * @param int $priority Priority
     * @return object Theme instance
     */
    function add_inline_css($css, $priority = 10)
    {
        return theme()->add_inline_css($css, $priority);
    }
}

if (!function_exists('add_inline_js')) {
    /**
     * Add inline JS
     * 
     * @param string $js JS code
     * @param string $position 'header' atau 'footer'
     * @param int $priority Priority
     * @return object Theme instance
     */
    function add_inline_js($js, $position = 'footer', $priority = 10)
    {
        return theme()->add_inline_js($js, $position, $priority);
    }
}

if (!function_exists('render_theme_css')) {
    /**
     * Render CSS assets
     * 
     * @param string $group Group name (default: 'theme')
     * @return string HTML link tags
     */
    function render_theme_css($group = 'theme')
    {
        return theme()->render_css($group);
    }
}

if (!function_exists('render_theme_js')) {
    /**
     * Render JS assets
     * 
     * @param string $position 'header' atau 'footer' (default: 'footer')
     * @param string $group Group name (default: 'theme')
     * @return string HTML script tags
     */
    function render_theme_js($position = 'footer', $group = 'theme')
    {
        return theme()->render_js($position, $group);
    }
}

if (!function_exists('theme_asset_exists')) {
    /**
     * Check if theme asset exists
     * 
     * @param string $path Asset path
     * @param string|null $theme Theme name
     * @return bool True jika asset exists
     */
    function theme_asset_exists($path, $theme = null)
{
        $CI =& get_instance();
        if (!isset($CI->theme_asset)) {
            $CI->load->library('theme_asset');
        }
        
        return $CI->theme_asset->exists($path, $theme);
    }
}

if (!function_exists('get_theme_assets')) {
    /**
     * Get all assets in theme
     * 
     * @param string|null $theme Theme name
     * @return array List of assets
     */
    function get_theme_assets($theme = null)
    {
        $CI =& get_instance();
        if (!isset($CI->theme_asset)) {
            $CI->load->library('theme_asset');
        }
        
        return $CI->theme_asset->get_theme_assets($theme);
    }
}

// ==============================================
// THEME LAYOUT FUNCTIONS
// ==============================================

if (!function_exists('theme_layout')) {
    /**
     * Get current layout name
     * 
     * @return string Current layout name
     */
    function theme_layout()
    {
        $CI =& get_instance();
        if (isset($CI->layout)) {
            return $CI->layout;
        }
        
        return theme()->get_layout();
    }
}

if (!function_exists('set_theme_layout')) {
    /**
     * Set theme layout
     * 
     * @param string $layout Layout name
     * @return object Theme instance
     */
    function set_theme_layout($layout)
    {
        return theme()->set_layout($layout);
    }
}

if (!function_exists('theme_has_layout')) {
    /**
     * Check if theme has specific layout
     * 
     * @param string $layout_name Layout name
     * @return bool True jika layout exists
     */
    function theme_has_layout($layout_name)
    {
        $CI =& get_instance();
        if (!isset($CI->layout_manager)) {
            $CI->load->library('layout_manager');
        }
        
        $layouts = $CI->layout_manager->get_all_layouts();
        return isset($layouts[$layout_name]);
    }
}

if (!function_exists('get_theme_layouts')) {
    /**
     * Get all available layouts for current theme
     * 
     * @return array List of layouts
     */
    function get_theme_layouts()
    {
        $CI =& get_instance();
        if (!isset($CI->layout_manager)) {
            $CI->load->library('layout_manager');
        }
        
        return $CI->layout_manager->get_all_layouts();
    }
}

// ==============================================
// THEME REGIONS & WIDGET AREAS
// ==============================================

if (!function_exists('theme_regions')) {
    /**
     * Get theme regions/positions
     * 
     * @return array Theme regions
     */
    function theme_regions()
{
        $info = theme_info();
        return isset($info['regions']) ? $info['regions'] : [];
    }
}

if (!function_exists('theme_has_region')) {
    /**
     * Check if theme has specific region
     * 
     * @param string $region_id Region ID
     * @return bool True jika region exists
     */
    function theme_has_region($region_id)
{
        return theme()->has_region($region_id);
    }
}

if (!function_exists('theme_region')) {
    /**
     * Render widgets in specific region
     * 
     * @param string $region_id Region ID
     * @return string Rendered widgets HTML
     */
    function theme_region($region_id)
{
        return theme()->render_region($region_id);
    }
}

if (!function_exists('theme_widgets')) {
    /**
     * Get widgets for specific position atau semua positions
     * 
     * @param string|null $position Position name (optional)
     * @return mixed Widgets output
     */
    function theme_widgets($position = null)
{
        return theme()->widgets($position);
    }
}

// ==============================================
// THEME OPTIONS & SETTINGS
// ==============================================

if (!function_exists('theme_option')) {
    /**
     * Get theme option value
     * 
     * @param string $key Option key
     * @param mixed $default Default value jika tidak ditemukan
     * @param string|null $theme Theme name (optional)
     * @return mixed Option value
     */
    function theme_option($key, $default = null, $theme = null)
{
        $CI =& get_instance();
        $CI->load->model('theme_model');
        
        if (!$theme) {
            $theme = theme_name();
        }
        
        return $CI->theme_model->get_setting($theme, $key, $default);
    }
}

if (!function_exists('theme_options')) {
    /**
     * Get all theme options
     * 
     * @param string|null $theme Theme name
     * @return array All theme options
     */
    function theme_options($theme = null)
{
        $CI =& get_instance();
        $CI->load->model('theme_model');
        
        if (!$theme) {
            $theme = theme_name();
        }
        
        return $CI->theme_model->get_all_settings($theme);
    }
}

if (!function_exists('update_theme_option')) {
    /**
     * Update theme option
     * 
     * @param string $key Option key
     * @param mixed $value Option value
     * @param string|null $theme Theme name
     * @return bool True jika berhasil
     */
    function update_theme_option($key, $value, $theme = null)
{
        $CI =& get_instance();
        $CI->load->model('theme_model');
        
        if (!$theme) {
            $theme = theme_name();
        }
        
        return $CI->theme_model->update_setting($theme, $key, $value);
    }
}

if (!function_exists('delete_theme_option')) {
    /**
     * Delete theme option
     * 
     * @param string $key Option key
     * @param string|null $theme Theme name
     * @return bool True jika berhasil
     */
    function delete_theme_option($key, $theme = null)
{
        $CI =& get_instance();
        $CI->load->model('theme_model');
        
        if (!$theme) {
            $theme = theme_name();
        }
        
        return $CI->theme_model->delete_setting($theme, $key);
    }
}

// ==============================================
// THEME PARTIALS & TEMPLATES
// ==============================================

if (!function_exists('theme_partial')) {
    /**
     * Render theme partial view
     * 
     * @param string $view Partial view name
     * @param array $data Data untuk view
     * @param bool $parse Parse template tags
     * @return string Rendered partial HTML
     */
    function theme_partial($view, $data = [], $parse = true)
{
        return theme()->partial($view, $data, $parse);
    }
}

if (!function_exists('theme_partial_exists')) {
    /**
     * Check if partial exists in theme
     * 
     * @param string $view Partial view name
     * @return bool True jika partial exists
     */
    function theme_partial_exists($view)
{
        $theme = theme();
        $partial_path = $theme->path . 'views/partials/' . $view . '.html';
        return file_exists($partial_path);
    }
}

if (!function_exists('theme_view')) {
    /**
     * Render theme view
     * 
     * @param string $view View name
     * @param array $data Data untuk view
     * @param bool $return Return atau output langsung
     * @return string|null Rendered view HTML
     */
    function theme_view($view, $data = [], $return = true)
{
        return theme()->render($view, $data, $return);
    }
}

if (!function_exists('theme_module_view')) {
    /**
     * Render module view dengan theme override
     * 
     * @param string $module Module name
     * @param string $view View name
     * @param array $data Data untuk view
     * @return string Rendered view HTML
     */
    function theme_module_view($module, $view, $data = [])
{
        $theme = theme();
        
        // Cek jika ada module view override di theme
        $module_view = 'modules/' . $module . '/' . $view;
        if ($theme->partial_exists($module_view)) {
            return $theme->partial($module_view, $data);
        }
        
        // Fallback ke view biasa
        $CI =& get_instance();
        return $CI->load->view($module . '/' . $view, $data, true);
    }
}

// ==============================================
// THEME CONDITIONAL FUNCTIONS
// ==============================================

if (!function_exists('is_admin_theme')) {
    /**
     * Check jika current theme adalah admin theme
     * 
     * @return bool True jika admin theme
     */
    function is_admin_theme()
{
        $theme_name = theme_name();
        $theme_info = theme_info();
        
        return ($theme_name == 'admin' || (isset($theme_info['type']) && $theme_info['type'] == 'admin'));
    }
}

if (!function_exists('is_mobile_theme')) {
    /**
     * Check jika current theme adalah mobile theme
     * 
     * @return bool True jika mobile theme
     */
    function is_mobile_theme()
{
        $theme_info = theme_info();
        return (isset($theme_info['type']) && $theme_info['type'] == 'mobile');
    }
}

if (!function_exists('is_frontend_theme')) {
    /**
     * Check jika current theme adalah frontend theme
     * 
     * @return bool True jika frontend theme
     */
    function is_frontend_theme()
{
        $theme_info = theme_info();
        return (isset($theme_info['type']) && $theme_info['type'] == 'frontend');
    }
}

if (!function_exists('is_theme_active')) {
    /**
     * Check jika theme aktif
     * 
     * @param string $theme_name Theme name
     * @return bool True jika theme aktif
     */
    function is_theme_active($theme_name)
{
        return (theme_name() == $theme_name);
    }
}

if (!function_exists('is_theme_exists')) {
    /**
     * Check jika theme exists
     * 
     * @param string $theme_name Theme name
     * @return bool True jika theme exists
     */
    function is_theme_exists($theme_name)
{
        $CI =& get_instance();
        $CI->load->library('theme');
        
        $themes = $CI->theme->get_all();
        return isset($themes[$theme_name]);
    }
}

// ==============================================
// THEME NAVIGATION & MENUS
// ==============================================

if (!function_exists('theme_menu_positions')) {
    /**
     * Get theme menu positions
     * 
     * @return array Menu positions
     */
    function theme_menu_positions()
{
        $info = theme_info();
        return isset($info['menu_positions']) ? $info['menu_positions'] : [];
    }
}

if (!function_exists('theme_menu')) {
    /**
     * Render theme menu
     * 
     * @param string $position Menu position
     * @param array $options Menu options
     * @return string Menu HTML
     */
    function theme_menu($position, $options = [])
{
        $CI =& get_instance();
        $CI->load->model('menu_model');
        
        $default_options = [
            'menu_class' => 'nav-menu',
            'depth' => 2,
            'container' => true,
            'container_class' => 'menu-container'
        ];
        
        $options = array_merge($default_options, $options);
        
        $menu = $CI->menu_model->get_by_position($position);
        
        if (!$menu) {
            return '';
        }
        
        $menu_items = $CI->menu_model->get_menu_items($menu['id']);
        
        // Build menu HTML
        $html = '';
        
        if ($options['container']) {
            $html .= '<div class="' . $options['container_class'] . '">';
        }
        
        $html .= '<ul class="' . $options['menu_class'] . '">';
        $html .= build_menu_items($menu_items, 0, $options['depth']);
        $html .= '</ul>';
        
        if ($options['container']) {
            $html .= '</div>';
        }
        
        return $html;
    }
}

if (!function_exists('build_menu_items')) {
    /**
     * Build menu items recursively
     * 
     * @param array $items Menu items
     * @param int $parent_id Parent ID
     * @param int $depth Maximum depth
     * @param int $current_depth Current depth
     * @return string Menu items HTML
     */
    function build_menu_items($items, $parent_id = 0, $depth = 2, $current_depth = 0)
{
        if ($current_depth >= $depth) {
            return '';
        }
        
        $html = '';
        $current_depth++;
        
        foreach ($items as $item) {
            if ($item['parent_id'] == $parent_id) {
                $has_children = false;
                foreach ($items as $child) {
                    if ($child['parent_id'] == $item['id']) {
                        $has_children = true;
                        break;
                    }
                }
                
                $html .= '<li class="menu-item' . ($has_children ? ' menu-item-has-children' : '') . '">';
                $html .= '<a href="' . $item['url'] . '"';
                
                if ($item['target'] == '_blank') {
                    $html .= ' target="_blank" rel="noopener"';
                }
                
                $html .= '>' . $item['title'] . '</a>';
                
                if ($has_children) {
                    $html .= '<ul class="sub-menu">';
                    $html .= build_menu_items($items, $item['id'], $depth, $current_depth);
                    $html .= '</ul>';
                }
                
                $html .= '</li>';
            }
        }
        
        return $html;
    }
}

// ==============================================
// THEME UTILITY FUNCTIONS
// ==============================================

if (!function_exists('theme_set_data')) {
    /**
     * Set theme data
     * 
     * @param mixed $key Data key atau array of data
     * @param mixed $value Data value (jika $key adalah string)
     * @return object Theme instance
     */
    function theme_set_data($key, $value = null)
{
        return theme()->set($key, $value);
    }
}

if (!function_exists('theme_get_data')) {
    /**
     * Get theme data
     * 
     * @param string|null $key Data key (optional)
     * @return mixed Theme data
     */
    function theme_get_data($key = null)
{
        $theme = theme();
        
        if ($key) {
            return $theme->$key;
        }
        
        return $theme->get_data();
    }
}

if (!function_exists('theme_body_class')) {
    /**
     * Get theme body classes
     * 
     * @param string|array $additional_classes Additional classes
     * @return string Body classes
     */
    function theme_body_class($additional_classes = '')
{
        $classes = [];
        
        // Add theme class
        $classes[] = 'theme-' . theme_name();
        
        // Add layout class
        $classes[] = 'layout-' . theme_layout();
        
        // Add controller and method classes
        $CI =& get_instance();
        $classes[] = 'controller-' . $CI->router->class;
        $classes[] = 'method-' . $CI->router->method;
        
        // Add additional classes
        if ($additional_classes) {
            if (is_array($additional_classes)) {
                $classes = array_merge($classes, $additional_classes);
            } else {
                $classes[] = $additional_classes;
            }
        }
        
        return implode(' ', array_unique($classes));
    }
}

if (!function_exists('theme_page_title')) {
    /**
     * Get page title dengan format yang tepat
     * 
     * @param string $title Page title
     * @param string $separator Title separator
     * @param bool $reverse Reverse order
     * @return string Formatted page title
     */
    function theme_page_title($title = '', $separator = ' - ', $reverse = false)
{
        $site_name = config_item('site_name');
        
        if ($title) {
            if ($reverse) {
                return $title . $separator . $site_name;
            } else {
                return $site_name . $separator . $title;
            }
        }
        
        return $site_name;
    }
}

if (!function_exists('theme_breadcrumbs')) {
    /**
     * Generate breadcrumbs
     * 
     * @param array $items Breadcrumb items
     * @param array $options Options
     * @return string Breadcrumbs HTML
     */
    function theme_breadcrumbs($items = [], $options = [])
{
        $default_options = [
            'home_text' => 'Home',
            'home_url' => base_url(),
            'separator' => '&raquo;',
            'wrapper_class' => 'breadcrumbs',
            'item_class' => 'breadcrumb-item',
            'active_class' => 'active'
        ];
        
        $options = array_merge($default_options, $options);
        
        if (empty($items)) {
            // Auto-generate breadcrumbs dari URI
            $CI =& get_instance();
            $segments = $CI->uri->segment_array();
            
            $items = [];
            $items[] = [
                'text' => $options['home_text'],
                'url' => $options['home_url']
            ];
            
            $url = $options['home_url'];
            foreach ($segments as $segment) {
                $url .= '/' . $segment;
                $items[] = [
                    'text' => humanize($segment),
                    'url' => $url
                ];
            }
            
            // Last item tidak memiliki URL
            if (!empty($items)) {
                $items[count($items) - 1]['url'] = null;
            }
        }
        
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="' . $options['wrapper_class'] . '">';
        
        foreach ($items as $index => $item) {
            $is_last = ($index == count($items) - 1);
            $class = $options['item_class'];
            
            if ($is_last) {
                $class .= ' ' . $options['active_class'];
            }
            
            $html .= '<li class="' . $class . '">';
            
            if (!$is_last && isset($item['url'])) {
                $html .= '<a href="' . $item['url'] . '">' . $item['text'] . '</a>';
            } else {
                $html .= '<span>' . $item['text'] . '</span>';
            }
            
            $html .= '</li>';
            
            if (!$is_last && $options['separator']) {
                $html .= '<li class="breadcrumb-separator">' . $options['separator'] . '</li>';
            }
        }
        
        $html .= '</ol>';
        $html .= '</nav>';
        
        return $html;
    }
}

// ==============================================
// THEME ADMIN FUNCTIONS
// ==============================================

if (!function_exists('theme_admin_url')) {
    /**
     * Get admin URL untuk theme management
     * 
     * @param string $path Admin path
     * @return string Full admin URL
     */
    function theme_admin_url($path = '')
{
        return site_url('admin/themes/' . ltrim($path, '/'));
    }
}

if (!function_exists('get_all_themes')) {
    /**
     * Get all available themes
     * 
     * @param string|null $type Filter by type
     * @return array List of themes
     */
    function get_all_themes($type = null)
{
        $theme = theme();
        $themes = $theme->get_all();
        
        if ($type) {
            $filtered = [];
            foreach ($themes as $theme_name => $theme_info) {
                if (isset($theme_info['type']) && $theme_info['type'] == $type) {
                    $filtered[$theme_name] = $theme_info;
                }
            }
            return $filtered;
        }
        
        return $themes;
    }
}

if (!function_exists('get_theme_screenshot')) {
    /**
     * Get theme screenshot URL
     * 
     * @param string $theme_name Theme name
     * @param string $default Default screenshot URL
     * @return string Screenshot URL
     */
    function get_theme_screenshot($theme_name, $default = '')
{
        $themes = get_all_themes();
        
        if (isset($themes[$theme_name]) && isset($themes[$theme_name]['screenshot'])) {
            $screenshot = $themes[$theme_name]['screenshot'];
            return theme_asset($screenshot, $theme_name);
        }
        
        return $default;
    }
}

// ==============================================
// THEME SHORTCUT FUNCTIONS
// ==============================================

if (!function_exists('t')) {
    /**
     * Shortcut untuk theme()
     * 
     * @return object Theme instance
     */
    function t()
    {
        return theme();
    }
}

if (!function_exists('ta')) {
    /**
     * Shortcut untuk theme_asset()
     * 
     * @param string $path Asset path
     * @param string|null $theme Theme name
     * @return string Asset URL
     */
    function ta($path, $theme = null)
    {
        return theme_asset($path, $theme);
    }
}

if (!function_exists('tc')) {
    /**
     * Shortcut untuk theme_css()
     * 
     * @param string $file CSS file
     * @param string|null $theme Theme name
     * @return string CSS URL
     */
    function tc($file, $theme = null)
    {
        return theme_css($file, $theme);
    }
}

if (!function_exists('tj')) {
    /**
     * Shortcut untuk theme_js()
     * 
     * @param string $file JS file
     * @param string|null $theme Theme name
     * @return string JS URL
     */
    function tj($file, $theme = null)
    {
        return theme_js($file, $theme);
    }
}

if (!function_exists('ti')) {
    /**
     * Shortcut untuk theme_img()
     * 
     * @param string $file Image file
     * @param string|null $theme Theme name
     * @return string Image URL
     */
    function ti($file, $theme = null)
    {
        return theme_img($file, $theme);
    }
}

if (!function_exists('tp')) {
    /**
     * Shortcut untuk theme_partial()
     * 
     * @param string $view Partial view
     * @param array $data Data
     * @return string Partial HTML
     */
    function tp($view, $data = [])
    {
        return theme_partial($view, $data);
    }
}

if (!function_exists('to')) {
    /**
     * Shortcut untuk theme_option()
     * 
     * @param string $key Option key
     * @param mixed $default Default value
     * @return mixed Option value
     */
    function to($key, $default = null)
    {
        return theme_option($key, $default);
    }
}

if (!function_exists('tr')) {
    /**
     * Shortcut untuk theme_region()
     * 
     * @param string $region Region ID
     * @return string Region HTML
     */
    function tr($region)
    {
        return theme_region($region);
    }
}

// ==============================================
// THEME DEBUG FUNCTIONS
// ==============================================

if (!function_exists('debug_theme_info')) {
    /**
     * Debug theme information
     * 
     * @param bool $return Return atau echo
     * @return string|null Debug information
     */
    function debug_theme_info($return = false)
    {
        $output = '<div style="border: 2px solid #007bff; padding: 15px; background: #e6f2ff; margin: 10px 0;">';
        $output .= '<h4 style="margin-top: 0; color: #007bff;">Theme Debug Information</h4>';
        
        $theme = theme();
        
        $output .= '<p><strong>Active Theme:</strong> ' . theme_name() . '</p>';
        $output .= '<p><strong>Theme Path:</strong> ' . theme_path() . '</p>';
        $output .= '<p><strong>Web Path:</strong> ' . theme_web_path() . '</p>';
        $output .= '<p><strong>Current Layout:</strong> ' . theme_layout() . '</p>';
        
        $info = theme_info();
        $output .= '<p><strong>Theme Info:</strong></p>';
        $output .= '<pre>' . print_r($info, true) . '</pre>';
        
        $output .= '<p><strong>Theme Regions:</strong></p>';
        $output .= '<pre>' . print_r(theme_regions(), true) . '</pre>';
        
        $output .= '</div>';
        
        if ($return) {
            return $output;
        }
        
        echo $output;
        return null;
    }
}

if (!function_exists('debug_theme_assets')) {
    /**
     * Debug theme assets
     * 
     * @param bool $return Return atau echo
     * @return string|null Debug information
     */
    function debug_theme_assets($return = false)
    {
        $output = '<div style="border: 2px solid #28a745; padding: 15px; background: #e6ffe6; margin: 10px 0;">';
        $output .= '<h4 style="margin-top: 0; color: #28a745;">Theme Assets Debug</h4>';
        
        $assets = get_theme_assets();
        
        $output .= '<p><strong>Total Assets:</strong> ' . count($assets) . '</p>';
        $output .= '<p><strong>Assets List:</strong></p>';
        $output .= '<ul>';
        
        foreach ($assets as $asset) {
            $output .= '<li>';
            $output .= $asset['path'] . ' (' . $asset['size_formatted'] . ')';
            $output .= ' - <a href="' . $asset['url'] . '" target="_blank">View</a>';
            $output .= '</li>';
        }
        
        $output .= '</ul>';
        $output .= '</div>';
        
        if ($return) {
            return $output;
        }
        
        echo $output;
        return null;
    }
}
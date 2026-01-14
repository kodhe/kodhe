<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_menu')) {
    /**
     * Get menu by ID, slug, or location
     */
    function get_menu($identifier, $by = 'id')
    {
        $CI =& get_instance();
        $CI->load->model('menu_model');
        
        switch ($by) {
            case 'id':
                return $CI->menu_model->get($identifier);
            case 'slug':
                return $CI->menu_model->get_by_slug($identifier);
            case 'location':
                // Get current theme
                $CI->load->library('theme');
                $theme = $CI->theme->get_active_theme();
                return $CI->menu_model->get_menu_by_location($identifier, $theme);
            default:
                return NULL;
        }
    }
}

if (!function_exists('render_menu')) {
    /**
     * Render menu as HTML
     */
    function render_menu($menu, $options = [])
    {
        if (is_numeric($menu)) {
            $menu = get_menu($menu, 'id');
        } elseif (is_string($menu) && !is_numeric($menu)) {
            // Check if it's a location
            if (in_array($menu, ['main', 'header', 'footer', 'sidebar'])) {
                $menu = get_menu($menu, 'location');
            } else {
                $menu = get_menu($menu, 'slug');
            }
        }
        
        if (!$menu || empty($menu['items'])) {
            return '';
        }
        
        $defaults = [
            'menu_class' => 'nav',
            'menu_id' => 'menu-' . ($menu['slug'] ?? $menu['id']),
            'item_class' => 'nav-item',
            'link_class' => 'nav-link',
            'dropdown_class' => 'dropdown',
            'dropdown_toggle_class' => 'dropdown-toggle',
            'dropdown_menu_class' => 'dropdown-menu',
            'active_class' => 'active',
            'depth' => 0,
            'max_depth' => 2,
            'current_url' => current_url()
        ];
        
        $options = array_merge($defaults, $options);
        
        return build_menu_html($menu['items'], $options);
    }
}

if (!function_exists('build_menu_html')) {
    /**
     * Build menu HTML recursively
     */
    function build_menu_html($items, $options, $level = 0)
    {
        if ($level >= $options['max_depth']) {
            return '';
        }
        
        $html = '<ul class="' . $options['menu_class'] . 
                ($level > 0 ? ' dropdown-menu' : '') . '">';
        
        foreach ($items as $item) {
            if (!$item['status']) {
                continue;
            }
            
            $has_children = !empty($item['children']);
            $is_active = is_menu_item_active($item, $options['current_url']);
            
            $li_classes = $options['item_class'];
            $link_classes = $options['link_class'];
            
            if ($has_children && $level < ($options['max_depth'] - 1)) {
                $li_classes .= ' ' . $options['dropdown_class'];
                $link_classes .= ' ' . $options['dropdown_toggle_class'];
            }
            
            if ($is_active) {
                $li_classes .= ' ' . $options['active_class'];
            }
            
            $html .= '<li class="' . trim($li_classes) . '">';
            
            // Build link attributes
            $attributes = [
                'href' => $item['url'],
                'class' => trim($link_classes),
                'title' => $item['title']
            ];
            
            if ($item['target'] && $item['target'] !== '_self') {
                $attributes['target'] = $item['target'];
            }
            
            if ($item['css_class']) {
                $attributes['class'] .= ' ' . $item['css_class'];
            }
            
            if ($has_children && $level < ($options['max_depth'] - 1)) {
                $attributes['data-bs-toggle'] = 'dropdown';
                $attributes['aria-expanded'] = 'false';
            }
            
            // Build link HTML
            $link_html = '<a';
            foreach ($attributes as $key => $value) {
                $link_html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
            }
            $link_html .= '>';
            
            // Add icon if exists
            if ($item['icon']) {
                $link_html .= '<i class="' . $item['icon'] . ' me-1"></i> ';
            }
            
            $link_html .= $item['title'];
            
            if ($has_children && $level < ($options['max_depth'] - 1)) {
                $link_html .= ' <span class="caret"></span>';
            }
            
            $link_html .= '</a>';
            
            $html .= $link_html;
            
            // Render children
            if ($has_children && $level < ($options['max_depth'] - 1)) {
                $html .= build_menu_html($item['children'], $options, $level + 1);
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        
        return $html;
    }
}

if (!function_exists('is_menu_item_active')) {
    /**
     * Check if menu item is active based on current URL
     */
    function is_menu_item_active($item, $current_url)
    {
        $item_url = $item['url'];
        
        // Remove protocol and domain for comparison
        $current_path = parse_url($current_url, PHP_URL_PATH);
        $item_path = parse_url($item_url, PHP_URL_PATH);
        
        if (!$item_path) {
            return FALSE;
        }
        
        // Exact match
        if ($current_path === $item_path) {
            return TRUE;
        }
        
        // Homepage special case
        if ($item_path === '/' && $current_path === '') {
            return TRUE;
        }
        
        // Partial match (for parent items)
        if (strpos($current_path, $item_path) === 0 && $item_path !== '/') {
            return TRUE;
        }
        
        return FALSE;
    }
}

if (!function_exists('menu_exists')) {
    /**
     * Check if menu exists
     */
    function menu_exists($identifier, $by = 'id')
    {
        $menu = get_menu($identifier, $by);
        return !empty($menu);
    }
}

if (!function_exists('get_menu_locations')) {
    /**
     * Get available menu locations from theme
     */
    function get_menu_locations()
    {
        $CI =& get_instance();
        $CI->load->library('theme');
        
        $theme_info = $CI->theme->get_info();
        $locations = [];
        
        if (isset($theme_info['menu_positions']) && is_array($theme_info['menu_positions'])) {
            $locations = $theme_info['menu_positions'];
        }
        
        // Add default locations
        $defaults = [
            'main' => 'Main Navigation',
            'header' => 'Header',
            'footer' => 'Footer',
            'sidebar' => 'Sidebar'
        ];
        
        return array_merge($defaults, $locations);
    }
}

if (!function_exists('get_current_menu_location')) {
    /**
     * Get menu for current location based on theme
     */
    function get_current_menu_location($location)
    {
        $CI =& get_instance();
        $CI->load->library('theme');
        $CI->load->model('menu_model');
        
        $theme = $CI->theme->get_active_theme();
        return $CI->menu_model->get_menu_by_location($location, $theme);
    }
}
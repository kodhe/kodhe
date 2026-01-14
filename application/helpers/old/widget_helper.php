<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WIDGET HELPER FUNCTIONS
 * 
 * Helper functions untuk mempermudah penggunaan widget system
 * seperti PyroCMS 2 Style
 */

// ==============================================
// WIDGET RENDERING FUNCTIONS
// ==============================================

if (!function_exists('widget')) {
    /**
     * Display widget by ID, instance ID, or render position
     * 
     * @param mixed $widget Widget ID, instance ID, atau position string
     * @param array $params Parameter tambahan untuk widget
     * @return string HTML output widget
     * 
     * Contoh penggunaan:
     * 1. {{ widget('blog_posts', {limit: 5}) }} - Widget by ID dengan parameter
     * 2. {{ widget(15) }} - Widget instance by ID (15 adalah instance_id)
     * 3. {{ widget('position:sidebar') }} - Semua widget di position 'sidebar'
     */
    function widget($widget, $params = [])
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        // Jika parameter berupa position (position:sidebar)
        if (is_string($widget) && strpos($widget, 'position:') === 0) {
            $position = substr($widget, 9);
            return $CI->widget_manager->render_position($position);
        }
        
        // Jika parameter numeric, anggap sebagai instance_id
        if (is_numeric($widget)) {
            $instance = $CI->widget_manager->get_instance($widget);
            if ($instance) {
                return $instance['widget']->run(array_merge($params, [
                    'options' => $instance['instance']['options']
                ]));
            }
            return '<div class="alert alert-warning">Widget instance not found: ' . $widget . '</div>';
        }
        
        // Jika parameter string, anggap sebagai widget_id
        if (is_string($widget)) {
            $widget_obj = $CI->widget_manager->get_widget($widget);
            if ($widget_obj) {
                return $widget_obj['object']->run($params);
            }
            return '<div class="alert alert-warning">Widget not found: ' . $widget . '</div>';
        }
        
        return '';
    }
}

if (!function_exists('widget_position')) {
    /**
     * Render semua widget dalam suatu position
     * 
     * @param string $position Nama position/region
     * @param array $options Opsi tambahan
     * @return string HTML output
     * 
     * Contoh: {{ widget_position('sidebar') }}
     */
    function widget_position($position, $options = [])
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $output = $CI->widget_manager->render_position($position);
        
        // Tambah wrapper jika ada opsi
        if (!empty($options)) {
            $wrapper_class = isset($options['wrapper_class']) ? $options['wrapper_class'] : '';
            $wrapper_id = isset($options['wrapper_id']) ? $options['wrapper_id'] : '';
            
            $wrapper = '<div class="widget-position widget-position-' . $position . ' ' . $wrapper_class . '"';
            if ($wrapper_id) {
                $wrapper .= ' id="' . $wrapper_id . '"';
            }
            $wrapper .= '>' . $output . '</div>';
            
            return $wrapper;
        }
        
        return $output;
    }
}

if (!function_exists('widget_area')) {
    /**
     * Render widget area (alias untuk widget_position dengan lebih banyak opsi)
     * 
     * @param string $area_id ID area/position
     * @param array $options Opsi area
     * @return string HTML output
     */
    function widget_area($area_id, $options = [])
    {
        $default_options = [
            'wrapper' => true,
            'wrapper_class' => 'widget-area',
            'wrapper_id' => 'area-' . $area_id,
            'title' => '',
            'title_tag' => 'h3',
            'title_class' => 'widget-area-title'
        ];
        
        $options = array_merge($default_options, $options);
        
        $output = widget_position($area_id);
        
        if (empty($output) && isset($options['empty_message'])) {
            $output = '<div class="widget-area-empty">' . $options['empty_message'] . '</div>';
        }
        
        if ($options['wrapper']) {
            $html = '<div class="' . $options['wrapper_class'] . '" id="' . $options['wrapper_id'] . '">';
            
            if ($options['title']) {
                $html .= '<' . $options['title_tag'] . ' class="' . $options['title_class'] . '">' 
                       . $options['title'] . '</' . $options['title_tag'] . '>';
            }
            
            $html .= $output;
            $html .= '</div>';
            
            return $html;
        }
        
        return $output;
    }
}

// ==============================================
// WIDGET INFORMATION FUNCTIONS
// ==============================================

if (!function_exists('widget_exists')) {
    /**
     * Check jika widget dengan ID tertentu ada
     * 
     * @param string $widget_id Widget ID
     * @return bool True jika widget ada
     */
    function widget_exists($widget_id)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $widget = $CI->widget_manager->get_widget($widget_id);
        return !empty($widget);
    }
}

if (!function_exists('widget_instance_exists')) {
    /**
     * Check jika widget instance dengan ID tertentu ada
     * 
     * @param int $instance_id Instance ID
     * @return bool True jika instance ada
     */
    function widget_instance_exists($instance_id)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $instance = $CI->widget_manager->get_instance($instance_id);
        return !empty($instance);
    }
}

if (!function_exists('get_widgets')) {
    /**
     * Get semua available widgets
     * 
     * @return array List of widgets
     */
    function get_widgets()
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        return $CI->widget_manager->get_widgets();
    }
}

if (!function_exists('get_widget')) {
    /**
     * Get informasi widget tertentu
     * 
     * @param string $widget_id Widget ID
     * @return array|null Widget info atau null jika tidak ditemukan
     */
    function get_widget($widget_id)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        return $CI->widget_manager->get_widget($widget_id);
    }
}

if (!function_exists('get_widget_instances')) {
    /**
     * Get semua widget instances
     * 
     * @param string|null $position Filter by position (optional)
     * @param bool $enabled_only Hanya widget yang enabled
     * @return array List of widget instances
     */
    function get_widget_instances($position = null, $enabled_only = true)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        if ($position) {
            return $CI->widget_manager->get_instances_by_position($position, $enabled_only);
        }
        
        return $CI->widget_manager->get_instances();
    }
}

if (!function_exists('get_widget_instance')) {
    /**
     * Get widget instance by ID
     * 
     * @param int $instance_id Instance ID
     * @return array|null Instance data atau null
     */
    function get_widget_instance($instance_id)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        return $CI->widget_manager->get_instance($instance_id);
    }
}

// ==============================================
// POSITION MANAGEMENT FUNCTIONS
// ==============================================

if (!function_exists('get_widget_positions')) {
    /**
     * Get semua available widget positions dari theme
     * 
     * @return array List of positions
     */
    function get_widget_positions()
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        return $CI->widget_manager->get_available_positions();
    }
}

if (!function_exists('get_positions_with_widgets')) {
    /**
     * Get semua positions dengan widget instances
     * 
     * @return array Positions dengan widget data
     */
    function get_positions_with_widgets()
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        return $CI->widget_manager->get_positions_with_widgets();
    }
}

if (!function_exists('has_widgets_in_position')) {
    /**
     * Check jika ada widget di position tertentu
     * 
     * @param string $position Position name
     * @return bool True jika ada widget
     */
    function has_widgets_in_position($position)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $widgets = $CI->widget_manager->get_widgets_by_position($position);
        return !empty($widgets);
    }
}

if (!function_exists('count_widgets_in_position')) {
    /**
     * Hitung jumlah widget di position tertentu
     * 
     * @param string $position Position name
     * @return int Jumlah widget
     */
    function count_widgets_in_position($position)
    {
        $CI =& get_instance();
        $CI->load->model('widget_model');
        
        return $CI->widget_model->count_by_position($position);
    }
}

// ==============================================
// WIDGET CREATION & MANAGEMENT FUNCTIONS
// ==============================================

if (!function_exists('create_widget_instance')) {
    /**
     * Create widget instance secara programmatic
     * 
     * @param string $widget_id Widget ID
     * @param array $data Instance data
     * @return int|bool Instance ID jika berhasil, false jika gagal
     */
    function create_widget_instance($widget_id, $data = [])
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $default_data = [
            'title' => '',
            'position' => '',
            'options' => [],
            'order' => 0,
            'enabled' => 1
        ];
        
        $instance_data = array_merge($default_data, $data);
        
        return $CI->widget_manager->create_instance($widget_id, $instance_data);
    }
}

if (!function_exists('update_widget_instance')) {
    /**
     * Update widget instance
     * 
     * @param int $instance_id Instance ID
     * @param array $data Data to update
     * @return bool True jika berhasil
     */
    function update_widget_instance($instance_id, $data)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        return $CI->widget_manager->update_instance($instance_id, $data);
    }
}

if (!function_exists('delete_widget_instance')) {
    /**
     * Delete widget instance
     * 
     * @param int $instance_id Instance ID
     * @return bool True jika berhasil
     */
    function delete_widget_instance($instance_id)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        return $CI->widget_manager->delete_instance($instance_id);
    }
}

if (!function_exists('enable_widget_instance')) {
    /**
     * Enable widget instance
     * 
     * @param int $instance_id Instance ID
     * @return bool True jika berhasil
     */
    function enable_widget_instance($instance_id)
    {
        return update_widget_instance($instance_id, ['enabled' => 1]);
    }
}

if (!function_exists('disable_widget_instance')) {
    /**
     * Disable widget instance
     * 
     * @param int $instance_id Instance ID
     * @return bool True jika berhasil
     */
    function disable_widget_instance($instance_id)
    {
        return update_widget_instance($instance_id, ['enabled' => 0]);
    }
}

if (!function_exists('toggle_widget_instance')) {
    /**
     * Toggle widget instance status
     * 
     * @param int $instance_id Instance ID
     * @return bool Status baru (true = enabled, false = disabled)
     */
    function toggle_widget_instance($instance_id)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $instance = $CI->widget_manager->get_instance($instance_id);
        if ($instance) {
            $new_status = !$instance['instance']['enabled'];
            update_widget_instance($instance_id, ['enabled' => $new_status]);
            return $new_status;
        }
        
        return false;
    }
}

// ==============================================
// WIDGET FORM & OPTIONS FUNCTIONS
// ==============================================

if (!function_exists('get_widget_form')) {
    /**
     * Get form HTML untuk widget options
     * 
     * @param string $widget_id Widget ID
     * @param array $current_values Current option values
     * @return string Form HTML
     */
    function get_widget_form($widget_id, $current_values = [])
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $widget = $CI->widget_manager->get_widget($widget_id);
        
        if ($widget && method_exists($widget['object'], 'get_form')) {
            return $widget['object']->get_form($current_values);
        }
        
        return '';
    }
}

if (!function_exists('widget_option')) {
    /**
     * Get widget option value
     * 
     * @param mixed $widget Widget ID atau instance ID
     * @param string $option_key Option key
     * @param mixed $default_value Default value jika tidak ditemukan
     * @return mixed Option value
     */
    function widget_option($widget, $option_key, $default_value = null)
    {
        $CI =& get_instance();
        
        // Jika numeric, anggap sebagai instance_id
        if (is_numeric($widget)) {
            $CI->load->library('widget_manager');
            $instance = $CI->widget_manager->get_instance($widget);
            
            if ($instance && isset($instance['instance']['options'][$option_key])) {
                return $instance['instance']['options'][$option_key];
            }
        }
        // Jika array, cari langsung di array
        else if (is_array($widget) && isset($widget['options'][$option_key])) {
            return $widget['options'][$option_key];
        }
        
        return $default_value;
    }
}

if (!function_exists('widget_title')) {
    /**
     * Get widget title
     * 
     * @param mixed $widget Widget instance ID atau array
     * @return string Widget title
     */
    function widget_title($widget)
    {
        if (is_numeric($widget)) {
            $instance = get_widget_instance($widget);
            if ($instance) {
                return $instance['instance']['title'];
            }
        } else if (is_array($widget) && isset($widget['title'])) {
            return $widget['title'];
        }
        
        return '';
    }
}

// ==============================================
// WIDGET TEMPLATE FUNCTIONS
// ==============================================

if (!function_exists('widget_template')) {
    /**
     * Render widget template secara manual
     * 
     * @param string $widget_name Widget name/template name
     * @param array $data Data untuk template
     * @return string Rendered template
     */
    function widget_template($widget_name, $data = [])
    {
        $CI =& get_instance();
        $CI->load->library('theme');
        
        // Coba load dari theme widgets
        $widget_view = 'widgets/' . $widget_name;
        
        if ($CI->theme->partial_exists($widget_view)) {
            return $CI->theme->partial($widget_view, $data);
        }
        
        // Fallback template
        return '<div class="widget widget-' . $widget_name . '">
            <h3>' . (isset($data['title']) ? $data['title'] : 'Widget') . '</h3>
            <div class="widget-content">
                Widget content for ' . $widget_name . '
            </div>
        </div>';
    }
}

if (!function_exists('widget_has_template')) {
    /**
     * Check jika widget memiliki template di theme
     * 
     * @param string $widget_name Widget name
     * @return bool True jika template ada
     */
    function widget_has_template($widget_name)
    {
        $CI =& get_instance();
        $CI->load->library('theme');
        
        return $CI->theme->widget_exists($widget_name);
    }
}

// ==============================================
// WIDGET STATISTICS FUNCTIONS
// ==============================================

if (!function_exists('get_widget_statistics')) {
    /**
     * Get widget usage statistics
     * 
     * @return array Widget statistics
     */
    function get_widget_statistics()
    {
        $CI =& get_instance();
        $CI->load->model('widget_model');
        
        return $CI->widget_model->get_statistics();
    }
}

if (!function_exists('count_total_widgets')) {
    /**
     * Hitung total widget instances
     * 
     * @return int Total widget instances
     */
    function count_total_widgets()
    {
        $instances = get_widget_instances();
        return count($instances);
    }
}

if (!function_exists('count_enabled_widgets')) {
    /**
     * Hitung total widget instances yang enabled
     * 
     * @return int Total enabled widgets
     */
    function count_enabled_widgets()
    {
        $CI =& get_instance();
        $CI->load->model('widget_model');
        
        $CI->db->where('enabled', 1);
        return $CI->db->count_all_results('widget_instances');
    }
}

if (!function_exists('get_most_used_widgets')) {
    /**
     * Get most frequently used widgets
     * 
     * @param int $limit Jumlah widget
     * @return array Most used widgets
     */
    function get_most_used_widgets($limit = 5)
    {
        $CI =& get_instance();
        $CI->load->model('widget_model');
        
        $CI->db->select('widget_id, COUNT(*) as count');
        $CI->db->group_by('widget_id');
        $CI->db->order_by('count', 'DESC');
        $CI->db->limit($limit);
        $query = $CI->db->get('widget_instances');
        
        return $query->result_array();
    }
}

// ==============================================
// WIDGET SHORTCUT FUNCTIONS
// ==============================================

if (!function_exists('blog_posts_widget')) {
    /**
     * Shortcut untuk blog posts widget
     * 
     * @param array $options Widget options
     * @return string Widget HTML
     */
    function blog_posts_widget($options = [])
    {
        $default_options = [
            'title' => 'Recent Posts',
            'limit' => 5,
            'show_date' => true
        ];
        
        $options = array_merge($default_options, $options);
        
        return widget('blog_posts', $options);
    }
}

if (!function_exists('navigation_widget')) {
    /**
     * Shortcut untuk navigation widget
     * 
     * @param array $options Widget options
     * @return string Widget HTML
     */
    function navigation_widget($options = [])
    {
        $default_options = [
            'title' => 'Navigation',
            'menu_id' => 1
        ];
        
        $options = array_merge($default_options, $options);
        
        return widget('navigation', $options);
    }
}

if (!function_exists('social_links_widget')) {
    /**
     * Shortcut untuk social links widget
     * 
     * @param array $options Widget options
     * @return string Widget HTML
     */
    function social_links_widget($options = [])
    {
        $default_options = [
            'title' => 'Follow Us',
            'layout' => 'icons'
        ];
        
        $options = array_merge($default_options, $options);
        
        return widget('social_links', $options);
    }
}

if (!function_exists('text_widget')) {
    /**
     * Shortcut untuk text widget (jika ada)
     * 
     * @param array $options Widget options
     * @return string Widget HTML
     */
    function text_widget($options = [])
    {
        $default_options = [
            'title' => '',
            'content' => ''
        ];
        
        $options = array_merge($default_options, $options);
        
        return widget('text', $options);
    }
}

// ==============================================
// WIDGET CONDITIONAL FUNCTIONS
// ==============================================

if (!function_exists('widget_if')) {
    /**
     * Conditional widget display
     * 
     * @param mixed $condition Condition untuk menampilkan widget
     * @param mixed $widget Widget ID, instance ID, atau position
     * @param array $params Widget parameters
     * @param mixed $else Alternative content jika condition false
     * @return string Widget HTML atau empty string
     */
    function widget_if($condition, $widget, $params = [], $else = '')
    {
        if ($condition) {
            return widget($widget, $params);
        }
        
        return $else;
    }
}

if (!function_exists('widget_unless')) {
    /**
     * Inverse conditional widget display
     * 
     * @param mixed $condition Condition untuk HIDE widget
     * @param mixed $widget Widget ID, instance ID, atau position
     * @param array $params Widget parameters
     * @param mixed $else Alternative content jika condition true
     * @return string Widget HTML atau empty string
     */
    function widget_unless($condition, $widget, $params = [], $else = '')
    {
        return widget_if(!$condition, $widget, $params, $else);
    }
}

if (!function_exists('widget_empty')) {
    /**
     * Display widget hanya jika position kosong
     * 
     * @param string $position Position name
     * @param mixed $widget Widget untuk display jika kosong
     * @param array $params Widget parameters
     * @return string Widget HTML atau empty string
     */
    function widget_empty($position, $widget, $params = [])
    {
        if (!has_widgets_in_position($position)) {
            return widget($widget, $params);
        }
        
        return '';
    }
}

// ==============================================
// WIDGET DEBUG FUNCTIONS
// ==============================================

if (!function_exists('debug_widget')) {
    /**
     * Debug widget information
     * 
     * @param mixed $widget Widget ID atau instance ID
     * @param bool $return Return atau echo
     * @return string|null Debug information
     */
    function debug_widget($widget, $return = false)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        $output = '<div class="widget-debug" style="border: 2px solid #f00; padding: 10px; background: #ffe6e6; margin: 10px 0;">';
        $output .= '<h4 style="margin-top: 0;">Widget Debug</h4>';
        
        if (is_numeric($widget)) {
            $instance = $CI->widget_manager->get_instance($widget);
            if ($instance) {
                $output .= '<p><strong>Instance ID:</strong> ' . $widget . '</p>';
                $output .= '<p><strong>Widget ID:</strong> ' . $instance['instance']['widget_id'] . '</p>';
                $output .= '<p><strong>Title:</strong> ' . $instance['instance']['title'] . '</p>';
                $output .= '<p><strong>Position:</strong> ' . $instance['instance']['position'] . '</p>';
                $output .= '<p><strong>Order:</strong> ' . $instance['instance']['order'] . '</p>';
                $output .= '<p><strong>Enabled:</strong> ' . ($instance['instance']['enabled'] ? 'Yes' : 'No') . '</p>';
                $output .= '<p><strong>Options:</strong></p>';
                $output .= '<pre>' . print_r($instance['instance']['options'], true) . '</pre>';
            } else {
                $output .= '<p><strong>Error:</strong> Instance not found: ' . $widget . '</p>';
            }
        } else {
            $widget_obj = $CI->widget_manager->get_widget($widget);
            if ($widget_obj) {
                $output .= '<p><strong>Widget ID:</strong> ' . $widget . '</p>';
                $output .= '<p><strong>Class:</strong> ' . $widget_obj['class'] . '</p>';
                $output .= '<p><strong>Info:</strong></p>';
                $output .= '<pre>' . print_r($widget_obj['info'], true) . '</pre>';
            } else {
                $output .= '<p><strong>Error:</strong> Widget not found: ' . $widget . '</p>';
            }
        }
        
        $output .= '</div>';
        
        if ($return) {
            return $output;
        }
        
        echo $output;
        return null;
    }
}

if (!function_exists('list_all_widgets')) {
    /**
     * List semua available widgets
     * 
     * @param bool $return Return atau echo
     * @return string|null List HTML
     */
    function list_all_widgets($return = false)
    {
        $widgets = get_widgets();
        
        $output = '<div class="all-widgets-list" style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
        $output .= '<h4>Available Widgets (' . count($widgets) . ')</h4>';
        $output .= '<ul style="list-style: none; padding-left: 0;">';
        
        foreach ($widgets as $widget_id => $widget) {
            $output .= '<li style="margin-bottom: 10px; padding: 5px; border-bottom: 1px solid #eee;">';
            $output .= '<strong>' . $widget['info']['title'] . '</strong> ';
            $output .= '<small>(ID: ' . $widget_id . ')</small><br>';
            $output .= '<em>' . $widget['info']['description'] . '</em><br>';
            $output .= '<small>Version: ' . $widget['info']['version'] . ' | ';
            $output .= 'Author: ' . $widget['info']['author'] . '</small>';
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

// ==============================================
// TEMPLATE TAG SHORTCUTS (untuk template engines)
// ==============================================

if (!function_exists('w')) {
    /**
     * Alias pendek untuk widget()
     * 
     * @param mixed $widget Widget ID, instance ID, atau position
     * @param array $params Widget parameters
     * @return string Widget HTML
     */
    function w($widget, $params = [])
    {
        return widget($widget, $params);
    }
}

if (!function_exists('wp')) {
    /**
     * Alias pendek untuk widget_position()
     * 
     * @param string $position Position name
     * @return string Widget HTML
     */
    function wp($position)
    {
        return widget_position($position);
    }
}

if (!function_exists('wa')) {
    /**
     * Alias pendek untuk widget_area()
     * 
     * @param string $area_id Area ID
     * @param array $options Options
     * @return string Widget HTML
     */
    function wa($area_id, $options = [])
    {
        return widget_area($area_id, $options);
    }
}
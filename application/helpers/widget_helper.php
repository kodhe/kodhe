<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('widget')) {
    function widget($widget, $params = [])
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        
        if (strpos($widget, 'position:') === 0) {
            $position = substr($widget, 9);
            return $CI->widget_manager->render_position($position);
        }
        
        if (is_numeric($widget)) {
            $instance = $CI->widget_manager->get_instance($widget);
            if ($instance) {
                return $instance['widget']->run(array_merge($params, [
                    'options' => $instance['instance']['options']
                ]));
            }
        }
        
        return $CI->widget_manager->render_widget($widget, $params);
    }
}

if (!function_exists('widget_position')) {
    function widget_position($position)
    {
        $CI =& get_instance();
        $CI->load->library('widget_manager');
        return $CI->widget_manager->render_position($position);
    }
}
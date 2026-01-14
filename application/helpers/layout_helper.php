<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('layout')) {
    function layout()
    {
        $CI =& get_instance();
        $CI->load->library('layout_manager');
        return $CI->layout_manager;
    }
}

if (!function_exists('render_layout')) {
    function render_layout($layout_name = 'default', $data = [])
    {
        $CI =& get_instance();
        $CI->load->library('widget_area');
        return $CI->widget_area->render_layout($layout_name, $data);
    }
}
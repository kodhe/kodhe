<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('theme')) {
    function theme()
    {
        $CI =& get_instance();
        return $CI->theme;
    }
}

if (!function_exists('theme_partial')) {
    function theme_partial($view, $data = [])
    {
        return theme()->partial($view, $data);
    }
}

if (!function_exists('theme_widget')) {
    function theme_widget($widget_name, $params = [])
    {
        return theme()->widget($widget_name, $params);
    }
}

if (!function_exists('theme_render')) {
    function theme_render($view = NULL, $data = [], $return = FALSE)
    {
        return theme()->render($view, $data, $return);
    }
}
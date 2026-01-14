<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('theme_asset')) {
    function theme_asset($path, $theme = NULL)
    {
        $CI =& get_instance();
        $CI->load->library('theme_asset');
        return $CI->theme_asset->url($path, $theme);
    }
}

if (!function_exists('theme_css')) {
    function theme_css($file, $theme = NULL)
    {
        return theme_asset('css/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('theme_js')) {
    function theme_js($file, $theme = NULL)
    {
        return theme_asset('js/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('theme_img')) {
    function theme_img($file, $theme = NULL)
    {
        return theme_asset('img/' . ltrim($file, '/'), $theme);
    }
}

if (!function_exists('render_css')) {
    function render_css($group = 'theme')
    {
        $CI =& get_instance();
        $CI->load->library('theme_asset');
        return $CI->theme_asset->render_css($group);
    }
}

if (!function_exists('render_js')) {
    function render_js($position = 'footer', $group = 'theme')
    {
        $CI =& get_instance();
        $CI->load->library('theme_asset');
        return $CI->theme_asset->render_js($position, $group);
    }
}
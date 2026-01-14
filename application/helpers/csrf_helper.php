<?php
// application/helpers/csrf_helper.php

if (!function_exists('get_csrf_token')) {
    function get_csrf_token()
    {
        $CI =& get_instance();
        if (isset($CI->security)) {
            return $CI->security->get_csrf_hash();
        }
        return '';
    }
}
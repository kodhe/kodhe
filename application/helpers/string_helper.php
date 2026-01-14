<?php
// File: application/helpers/string_helper.php

if (!function_exists('str_limit')) {
    /**
     * Limit string to specified length
     */
    function str_limit($string, $limit = 100, $end = '...')
    {
        if (mb_strlen($string) <= $limit) {
            return $string;
        }
        
        return mb_substr($string, 0, $limit) . $end;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date with human readable format
     */
    function format_date($date, $format = 'F d, Y')
    {
        return date($format, strtotime($date));
    }
}

if (!function_exists('gravatar')) {
    /**
     * Get Gravatar URL
     */
    function gravatar($email, $size = 80, $default = 'mp')
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}";
    }
}
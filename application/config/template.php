<?php
return [
    // Default template engine
    'default' => 'blade',
    // ====================
    // THEME SYSTEM
    // ====================
    'theme_enabled' => true,
    'theme_default' => 'default',
    'theme_admin' => 'admin',
    // Theme locations (searched in order)
    'theme_locations' => [
        APPPATH . 'themes',
        VIEWPATH,
        FCPATH . 'themes',
    ],  
];
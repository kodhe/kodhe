<?php
// application/config/hooks.php


$hook['post_controller_constructor'][] = array(
	'class'    => 'Trigger',
	'function' => 'index',
	'filename' => 'Trigger.php',
	'filepath' => 'hooks',
	'params'   => array()
);

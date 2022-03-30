<?php

namespace boctulus\Auth4WP;

/*
Plugin Name: Auth4WP
Description: Plugin que provee autenticación y securitización de rutas
Version: 1.0.1
Author: boctulus@gmail.com <Pablo>
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
	Evidenciar errores
*/


if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

include 'main.php';

<?php

namespace boctulus\Auth4WP;

/*
Plugin Name: Auth4WP
Description: Plugin que provee autenticación alternativa
Version: 1.0.0
Author: boctulus@gmail.com <Pablo>
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use boctulus\Auth4WP\libs\Strings;
use boctulus\Auth4WP\libs\Files;
use boctulus\Auth4WP\libs\Debug;
use boctulus\Auth4WP\libs\Mails;
use boctulus\Auth4WP\libs\Request;
use boctulus\Auth4WP\libs\Auth;

/*
	Evidenciar errores
*/


if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once __DIR__ . '/libs/Debug.php';
require_once __DIR__ . '/libs/Strings.php';
require_once __DIR__ . '/libs/Files.php';
require_once __DIR__ . '/libs/Request.php';
require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Arrays.php';
require_once __DIR__ . '/libs/Mails.php';
require_once __DIR__ . '/ajax.php';

require __DIR__ . '/config.php';


if (!function_exists('dd')){
	function dd($val, $msg = null, $pre_cond = null){
		Debug::dd($val, $msg, $pre_cond);
	}
}

if (!function_exists('here')){
	function here(){
		Debug::dd('HERE');
	}
}

function enqueues() 
{  
	//if (!is_home()){
		wp_register_script('bootstrap', Files::get_rel_path(). 'assets/js/bootstrap/bootstrap.bundle.min.js');
		wp_enqueue_script('bootstrap');

		wp_register_style('bootstrap', Files::get_rel_path() . 'assets/css/bootstrap/bootstrap.min.css');
		wp_enqueue_style('bootstrap');

		wp_register_style('main', Files::get_rel_path() . 'assets/css/main.css');
		wp_enqueue_style('main');
	//}
}

add_action( 'wp_enqueue_scripts', 'boctulus\Auth4WP\enqueues');


function shortcode_common(){
	?>
	<script>		
		function parseJSON(str) {
			try {
				return JSON.parse(str);
			}
			catch (e) {
				console.log('PARSING ERROR for ' + str);
				console.log(e);
				// Return a default object, or null based on use case.
				return null
			}
		}

		// On DOM is load
		document.addEventListener('DOMContentLoaded', () => 
    	{	
        	// ..
        });

	</script>
	<?php
}


function uth4wp_login($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/login.php';
}

function uth4wp_registration($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/login.php';
}

function uth4wp_rememberme($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/login.php';
}
	
// register shortcodes
add_shortcode('uth4wp_login', 'boctulus\Auth4WP\uth4wp_login');
add_shortcode('uth4wp_login', 'boctulus\Auth4WP\uth4wp_registration');
add_shortcode('uth4wp_login', 'boctulus\Auth4WP\uth4wp_rememberme');


/*
	Envio del correos en su template
*/

function send_email_verification_template(Array $data){
	// ...

	// podría abstraerse como view(Array $data)
	ob_start();
	include __DIR__ . '/views/email_verification_template.php';
	$content = ob_get_contents();
	ob_end_clean();

	$email    = $data['to_email'];
	$name     = $data['to_name'];
	$subject  = $data['subject'];

	$res = Mails::sendMail($email, $name, $subject, $content);
}

function send_email_rememberme_template(Array $data){
	// ...

	// podría abstraerse como view(Array $data)
	ob_start();
	include __DIR__ . '/views/email_rememberme_template.php';
	$content = ob_get_contents();
	ob_end_clean();

	$email    = $data['to_email'];
	$name     = $data['to_name'];
	$subject  = $data['subject'];

	$res = Mails::sendMail($email, $name, $subject, $content);
}









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

use boctulus\Auth4WP\libs\Strings;
use boctulus\Auth4WP\libs\Files;
use boctulus\Auth4WP\libs\Debug;
use boctulus\Auth4WP\libs\Mails;
use boctulus\Auth4WP\libs\Request;
use boctulus\Auth4WP\libs\Url;
use boctulus\Auth4WP\libs\Auth;
use boctulus\Auth4WP\libs\System;

/*
	Evidenciar errores
*/


if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once __DIR__ . '/email_cron.php';
require_once __DIR__ . '/libs/Debug.php';
require_once __DIR__ . '/libs/Strings.php';
require_once __DIR__ . '/libs/Files.php';
require_once __DIR__ . '/libs/Request.php';
require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Arrays.php';
require_once __DIR__ . '/libs/Mails.php';
require_once __DIR__ . '/libs/System.php';
require_once __DIR__ . '/ajax.php';

require __DIR__ . '/config.php';

// New table
require_once 'installer.php';


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
	<style>
		.login-form {
			width: 340px;
			margin: 30px auto;
		}

		.login-form form {
			margin-bottom: 15px;
			background: #f7f7f7;
			box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
			padding: 30px;
		}

		.login-form h2 {
			margin: 0 0 15px;
		}

		.login-form .hint-text {
			color: #777;
			padding-bottom: 15px;
			text-align: center;
		}

		.form-control, .btn {
			min-height: 50px;
			border-radius: 2px;
			font-size: 18px;
		}

		.login-btn {        
			font-weight: bold;
		}

		.or-seperator {
			font-size: 18px;
			margin: 20px 0 10px;
			text-align: center;
			border-top: 1px solid #ccc;
		}

		.or-seperator i {
			padding: 0 10px;
			background: #fff;
			position: relative;
			top: -15px;
			z-index: 1;
		}

		.social-btn .btn {
			margin: 12px 0;
			font-size: 18px;
			text-align: left; 
			line-height: 40px;       
		}

		.social-btn .btn i {
			float: left;
			margin: 11px 15px  0 5px;
			min-width: 15px;
		}

		.input-group-addon .fa{
			font-size: 20px;
		}    

		.card-primary.card-outline {
			border-top: 3px solid #007bff;
		}
	</style>

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

		function password_show_hide(id = 'password'){
			let icon_s  = document.getElementById('show_eye');
			let icon_h  = document.getElementById('hide_eye');
			let input   = document.getElementById(id);
			let input2  = input.cloneNode(false);

			if (input.type == 'password'){
				input2.type = 'text';
				icon_s.classList.add('d-none');
				icon_h.classList.remove('d-none');
			} else {
				input2.type = 'password';
				icon_h.classList.add('d-none');
				icon_s.classList.remove('d-none');
			}

			input.parentNode.replaceChild(input2,input);
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

	include __DIR__ . '/views/register.php';
}

function uth4wp_rememberme($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/rememberme.php';
}
	
// register shortcodes
add_shortcode('uth4wp_login', 'boctulus\Auth4WP\uth4wp_login');
add_shortcode('uth4wp_registration', 'boctulus\Auth4WP\uth4wp_registration');
add_shortcode('uth4wp_rememberme', 'boctulus\Auth4WP\uth4wp_rememberme');


/*
	Envio del correos en su template
*/

// function send_email_verification_template(Array $data){
// 	// ...

// 	// podría abstraerse como view(Array $data)
// 	ob_start();
// 	include __DIR__ . '/views/email_verification_template.php';
// 	$content = ob_get_contents();
// 	ob_end_clean();

// 	$email    = $data['to_email'];
// 	$name     = $data['to_name'];
// 	$subject  = $data['subject'];

// 	Mails::sendMail($email, $name, $subject, $content);
// }


// function send_email_rememberme_template(Array $data){
// 	// ...

// 	// podría abstraerse como view(Array $data)
// 	ob_start();
// 	include __DIR__ . '/views/email_rememberme_template.php';
// 	$content = ob_get_contents();
// 	ob_end_clean();

// 	$email    = $data['to_email'];
// 	$name     = $data['to_name'];
// 	$subject  = $data['subject'];

// 	Mails::sendMail($email, $name, $subject, $content);
// }


if (isset($date_timezone)){
	date_default_timezone_set($date_timezone);
}



// add_action( 'init', function () {
// 	if ( ! wp_next_scheduled( 'do_single_action' ) ) {
// 		wp_schedule_single_event( time() + 5, 'do_single_action' );
// 	}

// 	add_action( 'do_single_action', 'boctulus\Auth4WP\do_this_once' );

// 	/*
// 		Envio de correos
// 	*/
// 	function do_this_once() {
// 		global $wpdb;

// 		try {
// 			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}enqueued_mails ORDER BY id DESC LIMIT 10", ARRAY_A);

// 			foreach ($results as $r){
// 				$args = json_decode($r['data'], true);
// 				$wpdb->delete("{$wpdb->prefix}enqueued_mails", array( 'id' => $r['id'] ) );
			
// 				Mails::sendMail(...$args);
// 				sleep(2);
// 			}
// 		} catch (\Exception $e){
// 			Files::logger($e->getMessage());
// 		}

// 	}
// } );



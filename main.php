<?php

namespace boctulus\Auth4WP;

use boctulus\Auth4WP\libs\Strings;
use boctulus\Auth4WP\libs\Files;
use boctulus\Auth4WP\libs\Debug;
use boctulus\Auth4WP\libs\Mails;
use boctulus\Auth4WP\libs\Request;
use boctulus\Auth4WP\libs\Url;
use boctulus\Auth4WP\libs\Auth;
use boctulus\Auth4WP\libs\System;


require_once __DIR__ . '/email_cron.php'; // cron
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

function my_enqueues() 
{  
	//if (!is_home()){
        wp_register_script('jq', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js');
		wp_enqueue_script('jq');

		wp_register_script('axios', Files::get_rel_path(). 'assets/js/axios.js');
		wp_enqueue_script('axios');

		wp_register_script('bootstrap', Files::get_rel_path(). 'assets/js/bootstrap/bootstrap.bundle.min.js');
		wp_enqueue_script('bootstrap');

		wp_register_style('bootstrap', Files::get_rel_path() . 'assets/css/bootstrap/bootstrap.min.css');
		wp_enqueue_style('bootstrap');

		wp_register_style('main', Files::get_rel_path() . 'assets/css/main.css');
		wp_enqueue_style('main');

		wp_register_style('fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css');
		wp_enqueue_style('fa');
	//}
}

add_action( 'wp_enqueue_scripts', 'boctulus\Auth4WP\my_enqueues');


///security

function shortcode_common(){
	global $config;

	?>
	<style>
		#error_box {
			font-size:115%;
			text-align:left;
		}

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
		const base_url             = '<?= get_site_url() ?>';
		const login_redirection    = '<?= $config['redirections']['login'] ?>'
		const register_redirection = '<?= $config['redirections']['register'] ?>'
		const password_changed_redirection = '<?= $config['redirections']['password_changed'] ?? null ?>'
		const token_renewal        = '<?= get_site_url() . "wp-json/auth/v1/token" ?>'


		function parseJSON(str, default_val = null) {
			try {
				return JSON.parse(str);
			}
			catch (e) {
				console.log('PARSING ERROR for ' + str);
				console.log(e);
				// Return a default object, or null based on use case.
				return default_val;
			}
		}

		function addNotice(message, type = 'info', id_container = 'alert_container', replace = false){
			let types = ['info', 'danger', 'warning', 'success'];

			if (jQuery.inArray(type, types) == -1){
				throw "Tipo de notificación inválida para " + type;
			}

			if (message === ""){
				throw "Mensaje de notificación no puede quedar vacio";
				return;
			}

			let alert_container  = document.getElementById(id_container);
		
			if (replace){
				alert_container.innerHTML = '';
			}

			let code = (new Date().getTime()).toString();
			let id_notice = "notice-" + code;
			let id_close  = "close-"  + code;

			div = document.createElement('div');			
			div.innerHTML = `
			<div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert" id="${id_notice}">
				<span>
					${message}
				</span>
				<button type="button" class="btn-close notice" data-bs-dismiss="alert" aria-label="Close" id="${id_close}"></button>
			</div>`;

			alert_container.classList.add('mt-5');
			alert_container.prepend(div);

			document.getElementById(id_close).addEventListener('click', () => {
				let cnt = document.querySelectorAll('button.btn-close.notice').length -1;
				if (cnt == 0){
					alert_container.classList.remove('mt-5');
					alert_container.classList.add('mt-3');
				}
			});


			return id_notice;
		}

		function hideNotice(id_container = 'alert_container', notice_id = null){
			if (notice_id == null){
				let div  = document.querySelector(`#${id_container}`);
				div.innerHTML = '';
				div.classList.remove('mt-5');
			} else {
				document.getElementById(notice_id).remove();
			}
		}

		function clearNotices(id_container = 'alert_container'){
			hideNotice(id_container);
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

		function displayError(container_id, msg) {
			let el_cont = document.getElementById(container_id);

			if (msg) {    
				el_cont.innerHTML = msg;    
				el_cont.classList.remove("d-none");  
			}  
			else {    
				el_cont.classList.add("d-none");  
			}
		}
	</script>
	<?php
}


function auth4wp_login($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/login.php';
}

function auth4wp_registration($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/register.php';
}

function auth4wp_rememberme($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/rememberme.php';
}

// No se utiliza de momento porque se muestra un alert.
function auth4wp_rememberme_mail_sent($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/rememberme_mail_sent.php';
}

function auth4wp_rememberme_change_pass($atts = []) { 
	if (empty($atts)){
		$atts = [];
	}

	// require __DIR__ . '/config.php';
	shortcode_common();

	include __DIR__ . '/views/rememberme_change_pass.php';
}

	
// register shortcodes
add_shortcode('auth4wp_login', 'boctulus\Auth4WP\auth4wp_login');
add_shortcode('auth4wp_registration', 'boctulus\Auth4WP\auth4wp_registration');
add_shortcode('auth4wp_rememberme', 'boctulus\Auth4WP\auth4wp_rememberme');
add_shortcode('auth4wp_rememberme_change_pass', 'boctulus\Auth4WP\auth4wp_rememberme_change_pass');


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


if (isset($config['date_timezone'])){
	date_default_timezone_set($config['date_timezone']);
}


/*
	Completo urls 
*/
foreach ($config['url_pages'] as $key => $page){
	$config['url_pages'][$key] = trim($page, '/');

	if (!Strings::startsWith('http', $config['url_pages'][$key])){
		$config['url_pages'][$key] = get_site_url() . '/' . $config['url_pages'][$key];
	} 
}


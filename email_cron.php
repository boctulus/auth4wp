<?php

/*
	Envio de correo

	Programe un cron para la ejecución de este script. Tiene dos posibilidades:

	1) Hacer un request a http://su-sitio.com?sendmail=1

	2) Programar el cron para que ejecute:
	
	php php {ruta-al-wordpress}/wp-content/plugins/auth4wp/email_cron.php
*/

use boctulus\Auth4WP\libs\Mails;
use boctulus\Auth4WP\libs\Url;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', realpath(__DIR__ . '/../../..') . DIRECTORY_SEPARATOR);

	require_once ABSPATH . '/wp-config.php';
	require_once ABSPATH .'/wp-load.php';
}

require_once __DIR__ . '/libs/Mails.php';
require_once __DIR__ . '/libs/Url.php';

require __DIR__ . '/config.php';

// Mails::debug(4);
// Mails::silentDebug();


global $wpdb;

$cli = (php_sapi_name() == 'cli');

if ($cli){
	$go = true;
} else {
	$params = Url::queryString();
	$go = isset($params['sendmail']);
}

if ($go){
	$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}enqueued_mails WHERE locked_at = '0000-00-00 00:00:00' ORDER BY id ASC LIMIT 10", ARRAY_A);

	foreach ($results as $r){
		$args = json_decode($r['data'], true);

		$wpdb->update("{$wpdb->prefix}enqueued_mails", 
			[
				'locked_at' => (new \DateTime("NOW"))->format('Y-m-d H:i:s')
			],

			[ 
				'id' => $r['id'] 
			]
	 	);

		try {
			Mails::silentDebug();

			if ($cli){
				Mails::debug(4);
			}
			
			Mails::sendMail(...$args);

			if (!empty(Mails::errors())){
				if ($cli){
					dd(Mails::errors(), 'Errors');
				} 	

				throw new \Exception("Errores enviando email");
			}

		} catch (\Exception $e) {

			if ($cli){
				dd($e->getMessage());
			}

			Files::logger($e->getMessage());
			exit;
		}

		$wpdb->delete("{$wpdb->prefix}enqueued_mails", [ 'id' => $r['id'] ] );
		
	}

	exit;
}

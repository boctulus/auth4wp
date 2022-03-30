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

// ...

global $wpdb;

$cli = (php_sapi_name() == 'cli');

/*
	Delay inicial
*/

sleep(4);

if ($cli){
	$go = true;
} else {
	$params = Url::queryString();
	$go = isset($params['sendmail']);
}

if ($go){
	// La idea es no superar el max_execution_time que en muchos casos (hostings compartidos) es inmutable.
	$limit = $cli ? '' : ' LIMIT 5';

	$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}enqueued_mails WHERE locked_at = '0000-00-00 00:00:00' ORDER BY id ASC $limit", ARRAY_A);

	foreach ($results as $r){
		$args = json_decode($r['data'], true);

		// lock
		$wpdb->update("{$wpdb->prefix}enqueued_mails", 
			[
				'locked_at' => (new \DateTime("NOW"))->format('Y-m-d H:i:s')
			],

			[ 
				'id' => $r['id'] 
			]
	 	);

		try {
			Mails::debug(4);
			Mails::silentDebug();
			
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

			// unlock
			$wpdb->update("{$wpdb->prefix}enqueued_mails", 
			[
				'locked_at' => '0000-00-00 00:00:00'
			],

			[ 
				'id' => $r['id'] 
			]
	 	);

			Files::logger($e->getMessage());
			exit;
		}

		$wpdb->delete("{$wpdb->prefix}enqueued_mails", [ 'id' => $r['id'] ] );
		
	}

	exit;
}

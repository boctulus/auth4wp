<?php

require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Debug.php';

use boctulus\Auth4WP\libs\Strings;
use boctulus\Auth4WP\libs\Quotes;


/*
	REST

*/

function login(WP_REST_Request $req)
{
    $data = $req->get_body();

    try {
        if ($data === null){
            throw new \Exception("No se recibió la data");
        }

        $data = json_decode($data, true);

        if ($data === null){
            throw new \Exception("JSON inválido");
        }

        // $lang = $req->get_param('lang');

        $res = 'LOGGED IN';
        /// ....

        $res = new WP_REST_Response($res);
        $res->set_status(200);

        return $res;
    } catch (\Exception $e) {
        $error = new WP_Error(); 
        $error->add('general', $e->getMessage());

        return $error;
    }
    
}


/*
	/wp-json/auth/v1/xxxxx
*/
add_action('rest_api_init', function () {	  
	#	POST /wp-json/auth/v1/login
	register_rest_route('auth/v1', '/login', array(
		'methods' => 'POST',
		'callback' => 'login',
        'permission_callback' => '__return_true'
	) );

    register_rest_route('auth/v1', '/register', array(
		'methods' => 'POST',
		'callback' => 'register',
        'permission_callback' => '__return_true'
	) );

    register_rest_route('auth/v1', '/rememberme', array(
		'methods' => 'POST',
		'callback' => 'remembermer',
        'permission_callback' => '__return_true'
	) );

    register_rest_route('auth/v1', '/me', array(
        'methods' => 'POST',
        'callback' => 'me',
        'permission_callback' => '__return_true'
    ) );
} );




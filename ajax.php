<?php

require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Debug.php';
require_once __DIR__ . '/libs/vendors/PHP-JWT/Key.php';
require_once __DIR__ . '/libs/vendors/PHP-JWT/ExpiredException.php';
require_once __DIR__ . '/libs/vendors/PHP-JWT/BeforeValidException.php';
require_once __DIR__ . '/libs/vendors/PHP-JWT/SignatureInvalidException.php';
require_once __DIR__ . '/libs/vendors/PHP-JWT/JWK.php';
require_once __DIR__ . '/libs/vendors/PHP-JWT/JWT.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use boctulus\Auth4WP\libs\Auth;

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

        if (!isset($data['username'])){
            $error = new WP_Error(); 
            $error->add('req_username', 'El username es requerido');
            return $error;
        }

        if (!isset($data['password'])){
            $error = new WP_Error(); 
            $error->add('req_password', 'El password es requerido');
            return $error;
        }

        $user = $data['username'];
        $pass = $data['password'];

        $auth = wp_authenticate_username_password(null, $user, $pass);
        $errors = $auth->get_error_messages();

        if (!empty($errors)){
            $error = new WP_Error(); 

            foreach ($errors as $err){
                $error->add(401, TAGS_IN_RESPONSE ? $err : strip_tags($err));
            }

            return $error;
        }



        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload, $key, 'HS256');
        $decoded_arr = (array) JWT::decode($jwt, new Key($key, 'HS256'));







        $res = 'LOGGED IN '.  $user;
        
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




<?php

require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Debug.php';
require_once __DIR__ . '/libs/Auth.php';
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

// permitir usar correo@ en vez de username
function login(WP_REST_Request $req)
{
    global $jwt;

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
                $error->add(401, HTML_RESPONSE ? $err : strip_tags($err));
            }

            return $error;
        }

        $uid   = $auth->ID;
        $roles = Auth::userRoles($uid);

        $access  = Auth::gen_jwt([
            'uid'       => $uid, 
            'roles'     => $roles, 
        ], 'access_token'); 

        // el refresh no debe llevar ni roles ni permisos por seguridad !
        $refresh = Auth::gen_jwt([
            'uid' => $uid
        ], 'refresh_token');

        $res = [ 
            'access_token'=> $access,
            'token_type' => 'bearer', 
            'expires_in' => $jwt['access_token']['expiration_time'],
            'refresh_token' => $refresh,   
            'roles' => $roles,
            'uid' => $uid
        ];

        
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




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
use boctulus\Auth4WP\libs\Url;

/*
	REST

*/

add_filter( 'rest_authentication_errors', function( $result ) {
    $headers  = apache_request_headers();
    $endpoint = $_SERVER["REQUEST_URI"];
    $method   = $_SERVER['REQUEST_METHOD'];

    // Expecting "Bearer eyJ0eXAiOiJKV1QiLC...."
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? null;


    exit;

    return $result;
});

/*
    Funciona con username + password ó email + password
*/

function login(WP_REST_Request $req)
{
    global $jwt;

    $data = $req->get_body();

    try {
        if ($data === null) {
            throw new \Exception("No se recibió la data");
        }

        $data = json_decode($data, true);

        if ($data === null) {
            throw new \Exception("JSON inválido");
        }

        // $lang = $req->get_param('lang');

        $error = new WP_Error();

        if (!isset($data['username']) && !isset($data['email'])) {
            $error->add('req_username', 'Campos username o email son requeridos');
            return $error;
        }

        if (!isset($data['password'])) {
            $error->add('req_password', 'El password es requerido');
            return $error;
        }

        if (isset($data['username'])){
            $user_or_email = sanitize_text_field($data['username']);
        } else {
            $user_or_email = sanitize_text_field($data['email']);
        }
        
        $pass = sanitize_text_field($data['password']);

        if (strpos($user_or_email, '@') !== false) {
            $u_obj = get_user_by('email', $user_or_email);

            if (empty($u_obj)) {
                $error->add(401, 'Las credenciales son incorrectas');
                return $error;
            }

            $user = $u_obj->user_login;
        } else {
            $user = $user_or_email;
        }

        $auth = wp_authenticate_username_password(null, $user, $pass);
        $errors = $auth->get_error_messages();

        if (!empty($errors)) {
            $error = new WP_Error();

            foreach ($errors as $err) {
                //$error->add(401, HTML_RESPONSE ? $err : strip_tags($err));
                $error->add(401, 'Las credenciales son incorrectas');
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
            'access_token' => $access,
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


function register(WP_REST_Request $req)
{
    global $jwt;

    $data = $req->get_body();

    try {
        if ($data === null) {
            throw new \Exception("No se recibió la data");
        }

        $data = json_decode($data, true);

        if ($data === null) {
            throw new \Exception("JSON inválido");
        }

        $error = new WP_Error();

        if (!isset($data['username'])) {
            $error->add(400, 'El username es requerido');
            return $error;
        }

        if (!isset($data['email'])) {
            $error->add(400, 'El email es requerido');
            return $error;
        }

        if (!isset($data['password'])) {
            $error->add(400, 'El password es requerido');
            return $error;
        }

        $username = sanitize_text_field($data['username']);
        $email    = sanitize_text_field($data['email']);
        $password = sanitize_text_field($data['password']);

        $uid = username_exists($username);

        if (!$uid && email_exists($email) == false) {
            $uid = wp_create_user($username, $password, $email);

            if (!is_wp_error($uid)) {
                // Ger User Meta Data (Sensitive, Password included. DO NOT pass to front end.)
                $user = get_user_by('id', $uid);
            
                // $user->set_role($role);
                $user->set_role('subscriber');
            
                // WooCommerce specific code
                if (class_exists('WooCommerce')) {
                    $user->set_role('customer');
                }

                $uid   = $user->ID;
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
                    'access_token' => $access,
                    'token_type' => 'bearer',
                    'expires_in' => $jwt['access_token']['expiration_time'],
                    'refresh_token' => $refresh,
                    'roles' => $roles,
                    'uid' => $uid
                ];

                // Ger User Data (Non-Sensitive, Pass to front end.)
                $res['code'] = 201;
                $res['message'] = "Registración exitosa";
            } else {
                return $uid;
            }
        
        } else {
            $error->add(406, "Email ya existe. Intente restablecer contraseña", array('status' => 400));
            return $error;
        }
        
        return new WP_REST_Response($res, 201);

    } catch (\Exception $e) {
        $error->add(500, $e->getMessage());
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
    ));

    register_rest_route('auth/v1', '/register', array(
        'methods' => 'POST',
        'callback' => 'register',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('auth/v1', '/rememberme', array(
        'methods' => 'POST',
        'callback' => 'remembermer',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('auth/v1', '/me', array(
        'methods' => 'POST',
        'callback' => 'me',
        'permission_callback' => '__return_true'
    ));
});

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
use boctulus\Auth4WP\libs\Mails;

/*
	REST

*/

add_filter( 'rest_authentication_errors', function( $result ) {
    global $jwt, $endpoints;

    $headers          = apache_request_headers();
    $current_endpoint = $_SERVER["REQUEST_URI"];
    $method           = $_SERVER['REQUEST_METHOD'];

    // Expecting "Bearer eyJ0eXAiOiJKV1QiLC...."
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    $error = new WP_Error();

    if (!empty($endpoints)){
        foreach ($endpoints as $endpoint){  
            if ($current_endpoint != $endpoint['slug']){
                //dd("$current_endpoint != $endpoint");
                continue;
            }

            if (empty($auth)){
                $error->add(401, "El header 'Authorization' con el token JWT es requerido");
                return $error;
            }
    
            try {
                list($token) = sscanf($auth, 'Bearer %s');
    
                /*
                    array (
                    'alg' => 'HS256',
                    'typ' => 'JWT',
                    'iat' => 1648083670,
                    'exp' => 1657083670,
                    'ip' => '127.0.0.1',
                    'user_agent' => 'PostmanRuntime/7.29.0',
                    'uid' => 9,
                    'roles' => 
                    array (
                        0 => 'editor',
                    ),
                )
                */
                $payload = JWT::decode($token, new Key($jwt['access_token']['secret_key'], $jwt['access_token']['encryption']));
    
                if (empty($payload)){
                    $error->add(401, 'Unauthorized');
                    return $error;
                }                     
    
                if (empty($payload->uid)){
                    $error->add(401, 'Unauthorized');
                    return $error;
                }
    
                if (empty($payload->roles)){
                    $error->add(401, 'Unauthorized');
                    return $error;
                }

                if ($payload->exp < time()){
                    $error->add(401, 'Token expired, please log in');
                    return $error;
                }
                
            } catch (\Exception $e){    
                $error->add(500, $e->getMessage());
                return $error;
            }

            // Ej:
            $authorized_roles = $endpoint['roles'];

            $authorized = false;
            foreach ($payload->roles as $role){
                if (in_array($role, $authorized_roles)){
                    $authorized = true;
                    break;
                }
            }

            if (!$authorized){
                $error->add(403, 'Forbidden');
                return $error;
            }
        }
        
    }

    return $result;
});

function token()
{
    global $jwt;

    $headers = apache_request_headers();

    // Expecting "Bearer eyJ0eXAiOiJKV1QiLC...."
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    $error = new WP_Error();

    if (empty($auth)){
        $error->add(401, "El header 'Authorization' con el 'fressh' token JWT es requerido");
        return $error;
    }

    try {
        list($token) = sscanf($auth, 'Bearer %s');

        /*
            array (
            'alg' => 'HS256',
            'typ' => 'JWT',
            'iat' => 1648083670,
            'exp' => 1657083670,
            'ip' => '127.0.0.1',
            'user_agent' => 'PostmanRuntime/7.29.0',
            'uid' => 9,
            'roles' => 
            array (
                0 => 'editor',
            ),
        )
        */
        $payload = JWT::decode($token, new Key($jwt['refresh_token']['secret_key'], $jwt['refresh_token']['encryption']));

        if (empty($payload)){
            $error->add(401, 'Unauthorized.');
            return $error;
        }                     

        if (empty($payload->uid)){
            $error->add(401, 'Unauthorized..');
            return $error;
        }

        if (empty($payload->roles)){
            $error->add(401, 'Unauthorized..');
            return $error;
        }

        if ($payload->exp < time()){
            $error->add(401, 'Token expired, please log in');
            return $error;
        }
        
    } catch (\Exception $e){    
        $error->add(500, $e->getMessage());
        return $error;
    }

    //dd($payload);

    $uid   = $payload->uid;
    $roles = $payload->roles;

    $access  = Auth::gen_jwt([
        'uid'       => $uid,
        'roles'     => $roles,
    ], 'access_token');

    $refresh = Auth::gen_jwt([
        'uid'       => $uid,
        'roles'     => $roles, // *
    ], 'refresh_token');

    $res = [
        'access_token' => $access,
        'token_type' => 'bearer',
        'expires_in' => $jwt['access_token']['expiration_time'],
        'refresh_token' => $refresh,
        'roles' => $roles,
        'uid' => $uid,
        'message' => 'Renovación de tokens exitosa'
    ];

    $res = new WP_REST_Response($res);
    $res->set_status(200);

    return $res;
}


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

        $data = Url::bodyDecode($data);
        
        // $lang = $req->get_param('lang');

        $error = new WP_Error();

        if (!isset($data['username']) && !isset($data['email'])) {
            $error->add(400, 'Campos username o email son requeridos');
            return $error;
        }

        if (!isset($data['password'])) {
            $error->add(400, 'El password es requerido');
            return $error;
        }

        if (isset($data['username'])){
            $user_or_email = sanitize_text_field($data['username']);
        } else {
            $user_or_email = sanitize_text_field($data['email']);
        }
        
        $pass = $data['password'];

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

        $refresh = Auth::gen_jwt([
            'uid'   => $uid,
            'roles' => $roles,
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
            throw new \Exception("No data");
        }

        $data = Url::bodyDecode($data);

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
        $password = $data['password'];

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


                $refresh = Auth::gen_jwt([
                    'uid'   => $uid,
                    'roles' => $roles, // *
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
                $res['message'] = "Successful registration";
            } else {
                return $uid;
            }
        
        } else {
            $error->add(406, "Email already exists. Try to reset password.", array('status' => 400));
            return $error;
        }
        
        return new WP_REST_Response($res, 201);

    } catch (\Exception $e) {
        $error->add(500, $e->getMessage());
    }
}

function get_me(){
    global $jwt;

    $headers = apache_request_headers();

    // Expecting "Bearer eyJ0eXAiOiJKV1QiLC...."
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    $error = new WP_Error();

    if (empty($auth)){
        $error->add(401, "El header 'Authorization' con el 'access' token JWT es requerido");
        return $error;
    }

    try {
        list($token) = sscanf($auth, 'Bearer %s');

        /*
            array (
            'alg' => 'HS256',
            'typ' => 'JWT',
            'iat' => 1648083670,
            'exp' => 1657083670,
            'ip' => '127.0.0.1',
            'user_agent' => 'PostmanRuntime/7.29.0',
            'uid' => 9,
            'roles' => 
            array (
                0 => 'editor',
            ),
        )
        */
        $payload = JWT::decode($token, new Key($jwt['access_token']['secret_key'], $jwt['access_token']['encryption']));

        if (empty($payload)){
            $error->add(401, 'Unauthorized.');
            return $error;
        }                     

        if (empty($payload->uid)){
            $error->add(401, 'Unauthorized..');
            return $error;
        }

        if (empty($payload->roles)){
            $error->add(401, 'Unauthorized..');
            return $error;
        }

        if ($payload->exp < time()){
            $error->add(401, 'Token expired, please log in');
            return $error;
        }
        
    } catch (\Exception $e){    
        $error->add(500, $e->getMessage());
        return $error;
    }

    $u = get_user_by('ID', $payload->uid);


    $res = [
        'uid'           => $payload->uid,
        'username'      => $u->user_login,
        'roles'         => $payload->roles,
        'registered_at' => $u->user_registered
    ];

    $res = new WP_REST_Response($res);
    $res->set_status(200);

    return $res;
}


/*
    En principio solo para actualizar password

    Requiere del "refresh" token ***
*/
function patch_me(WP_REST_Request $req){
    global $jwt;

    $headers = apache_request_headers();

    // Expecting "Bearer eyJ0eXAiOiJKV1QiLC...."
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    $error = new WP_Error();

    if (empty($auth)){
        $error->add(401, "The 'Authorization' header with the JWT 'access' token is required");
        return $error;
    }

    try {
        list($token) = sscanf($auth, 'Bearer %s');

        /*
            array (
            'alg' => 'HS256',
            'typ' => 'JWT',
            'iat' => 1648083670,
            'exp' => 1657083670,
            'ip' => '127.0.0.1',
            'user_agent' => 'PostmanRuntime/7.29.0',
            'uid' => 9,
            'roles' => 
            array (
                0 => 'editor',
            ),
        )
        */
        $payload = JWT::decode($token, new Key($jwt['refresh_token']['secret_key'], $jwt['refresh_token']['encryption']));

        if (empty($payload)){
            $error->add(401, 'Unauthorized.');
            return $error;
        }                     

        if (empty($payload->uid)){
            $error->add(401, 'Unauthorized..');
            return $error;
        }

        if (empty($payload->roles)){
            $error->add(401, 'Unauthorized..');
            return $error;
        }

        if ($payload->exp < time()){
            $error->add(401, 'Token expired, please log in');
            return $error;
        }

        // ...

        $data = $req->get_body();

        if ($data === null) {
            throw new \Exception("No data");
        }

        $data = Url::bodyDecode($data);

        if (!isset($data['password'])){
            $error = new WP_Error();

            $error->add(400, 'Password is required');
            return $error;
        }

        $pass = $data['password'];

        // not working
        wp_set_password($pass, $payload->uid);

        $res = [
            'message' => 'Successful password change'
        ];

        $res = new WP_REST_Response($res);
        $res->set_status(200);

        return $res;
        
    } catch (\Exception $e){    
        $error->add(500, $e->getMessage());
        return $error;
    }
}


function rememberme(WP_REST_Request $req)
{
    global $jwt;

    $data = $req->get_body();

    try {
        if ($data === null) {
            throw new \Exception("No data");
        }

        $data = Url::bodyDecode($data);

        $error = new WP_Error();

        if (!isset($data['email'])) {
            $error->add(400, 'Email is required');
            return $error;
        }

        $u = get_user_by('email', $data['email']);
       
         // envio del link al correo
        if (!empty($u)){           
            $email_token = Auth::gen_jwt_rememberme($u->ID);
            $base_url = get_site_url();

            $link = "$base_url/wp-json/auth/v1/change_pass_by_link/$email_token";

            $body = "Hola!
            <p/>Para re-establecer la password siga el <a href=\"$link\">enlace</a></p>";

            Mails::sendMail($data['email'], '', 'Recuperación de password', $body);

            // dd(Mails::status());
            // dd(Mails::errors());

            //dd("Enviando hiperlink: $link");
        }

        $res = [
            'message' => 'You will receive a verification email with a hyperlink'
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
    Recibo un token y cambio la contraseña
*/
function change_pass_by_link(WP_REST_Request $req){
    global $jwt;

    $token = $req->get_param('token');

    try {    
        $payload = JWT::decode($token, new Key($jwt['email_token']['secret_key'], $jwt['email_token']['encryption']));

        $error = new WP_Error();

        if (empty($payload)){
            $error->add(401, 'Unauthorized');
            return $error;
        }                     

        if (empty($payload->uid)){
            $error->add(401, 'Unauthorized.');
            return $error;
        }

        if ($payload->exp < time()){
            $error->add(401, 'Token expired');
            return $error;
        }

        $u = get_user_by('id', $payload->uid);

        $uid   = $payload->uid;
        $roles = $u->roles;

        $access  = Auth::gen_jwt([
            'uid'       => $uid,
            'roles'     => $roles,
        ], 'access_token');

        $refresh = Auth::gen_jwt([
            'uid' => $uid,
            'roles' => $roles, // *
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
        
    } catch (\Exception $e){    
        $error->add(500, $e->getMessage());
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
    ));

    register_rest_route('auth/v1', '/register', array(
        'methods' => 'POST',
        'callback' => 'register',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('auth/v1', '/token', array(
        'methods' => 'POST',
        'callback' => 'token',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('auth/v1', '/me', array(
        'methods' => 'GET',
        'callback' => 'get_me',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('auth/v1', '/me', array(
        'methods' => 'PATCH',
        'callback' => 'patch_me',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('auth/v1', '/rememberme', array(
        'methods' => 'POST',
        'callback' => 'rememberme',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('auth/v1', '/change_pass_by_link/(?P<token>[a-zA-Z0-9._\-]+)', array(
        'methods' => 'GET',
        'callback' => 'change_pass_by_link',
        'permission_callback' => '__return_true'
    ));

});

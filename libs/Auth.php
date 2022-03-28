<?php declare(strict_types=1);

namespace boctulus\Auth4WP\libs;

class Auth 
{    
    static function userRoles($user_id)
    {
        $user_meta  = get_userdata($user_id);
        $user_roles = $user_meta->roles;

        return $user_roles;
    }

    static function gen_jwt(array $props, string $token_type, int $expires_in = null){
        global $config;

        if (!in_array($token_type, ['access_token', 'refresh_token', 'email_token'])){
            throw new \InvalidArgumentException("Token type should be access_token|refresh_token|email_token");
        }

        $time = time();

        $payload = [
            'alg' => $config['jwt'][$token_type]['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + ($expires_in != null ? $expires_in : $config['jwt'][$token_type]['expiration_time']),
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent()
        ];
        
        $payload = array_merge($payload, $props);

        return \Firebase\JWT\JWT::encode($payload, $config['jwt'][$token_type]['secret_key'],  $config['jwt'][$token_type]['encryption']);
    }

    static function gen_jwt_email_conf(string $email, array $roles, array $perms, $uid){
        global $config;

        $time = time();

        $payload = [
            'alg' => $config['jwt']['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $config['jwt']['email_token']['expires_in'],
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent(),
            'email' => $email,
            'roles' => $roles,
            'permissions' => $perms
         ];

        return \Firebase\JWT\JWT::encode($payload, $config['jwt']['email_token']['secret_key'],  $config['jwt']['email_token']['encryption']);
    }

    static function gen_jwt_rememberme($uid){
        global $config;

        $time = time();

        $payload = [
            'alg' => $config['jwt']['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $config['jwt']['email_token']['expires_in'],
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent(),
            'uid' => $uid
         ];

        return \Firebase\JWT\JWT::encode($payload, $config['jwt']['email_token']['secret_key'],  $config['jwt']['email_token']['encryption']);
    }
}
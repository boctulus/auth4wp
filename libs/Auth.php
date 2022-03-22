<?php declare(strict_types=1);

namespace boctulus\Auth4WP\libs;

class Auth 
{
    public function __construct()
    {
        global $jwt;
        $this->$jwt = $jwt;
    }

    protected function userRoles($user_id)
    {
        $user_meta  = get_userdata($user_id);
        $user_roles = $user_meta->roles;

        return $user_roles;
    }

    protected function gen_jwt(array $props, string $token_type, int $expires_in = null){
        $time = time();

        $payload = [
            'alg' => $this->jwt[$token_type]['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + ($expires_in != null ? $expires_in : $this->jwt[$token_type]['expiration_time']),
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent()
        ];
        
        $payload = array_merge($payload, $props);

        return \Firebase\JWT\JWT::encode($payload, $this->jwt[$token_type]['secret_key'],  $this->jwt[$token_type]['encryption']);
    }

    protected function gen_jwt_email_conf(string $email, array $roles, array $perms, $uid){
        $time = time();

        $payload = [
            'alg' => $this->jwt['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $this->jwt['email_token']['expires_in'],
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent(),
            'email' => $email,
            'roles' => $roles,
            'permissions' => $perms
         ];

        return \Firebase\JWT\JWT::encode($payload, $this->jwt['email_token']['secret_key'],  $this->jwt['email_token']['encryption']);
    }

    protected function gen_jwt_rememberme($uid){
        $time = time();

        $payload = [
            'alg' => $this->jwt['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $this->jwt['email_token']['expires_in'],
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent(),
            'uid' => $uid
         ];

        return \Firebase\JWT\JWT::encode($payload, $this->jwt['email_token']['secret_key'],  $this->jwt['email_token']['encryption']);
    }
}
<?php

/*
    Auth4WP
    boctulus@gmail.com
*/


/*
    Se podría crear un endpoint donde pueda enviar los endpoints que serán securitizados y los roles admitidos
    
    POST /auth/v1/endpoints

    [
        {
            "slug": "/wp-json/cotizar/v1/dollar",
            "roles": ["editor", "administrator"]
        },

        // otros endpoints
    ]

    Esto implica que ese endpoint deba ser accesile (y visible) solo para un "administrator" y 
    que la data deba almacenarse en la base de datos !
*/

/*
    Las siguientes constantes podrían estar en el wp-config.php
*/

if (!defined('TOKENS_ACCSS_SECRET_KEY')){
    define('TOKENS_ACCSS_SECRET_KEY', 'adf0000000000000010101');
    define('TOKENS_REFSH_SECRET_KEY', 'blabla)@11........3333');
    define('TOKENS_EMAIL_SECRET_KEY', ' dhdh994Alo3340303...3');
}

if (!defined('MAIL_HOST')){
    define('MAIL_DRIVER', 'smtp');
    define('MAIL_HOST','smtp.gmail.com');
    define('MAIL_PORT', 587);
    define('MAIL_USERNAME', 'xxxxxxxxx@gmail.com');
    define('MAIL_PASSWORD', 'XXXXXXXXXXXXX');
    define('MAIL_AUTH', true);
    define('MAIL_ENCRYPTION', 'tls');

    define('MAIL_DEFAULT_FROM_ADDR', 'xxxxxxxxx@gmail.com');
    define('MAIL_DEFAULT_FROM_NAME', 'No responder');
}


/* 
    Securitized endpoints
*/

$config = [
    'endpoints' => [

        // Usar para ´testear´ el funcionamiento
        [
            "slug" => "/wp-json/wp/v2/media",
            "roles" => [
                //"subscriber",
                //"editor",
                "repartidor",
                "administrator"
            ]
        ],

        [
            "slug" => "/wp-json/despachos/v1/crear",
            "roles" => [
                "repartidor",
                "administrator"
            ]
        ],
    
        // more endpoints to be securitized
    ],

    'jwt' => [
        'access_token' => [
            'secret_key' 		=> TOKENS_ACCSS_SECRET_KEY,
            'expiration_time'	=> 60 * 15 * 10000,   // seconds (normalmente 60 * 15)
            'encryption'		=> 'HS256'			
        ],
    
        'refresh_token' => [
            'secret_key'		=> TOKENS_REFSH_SECRET_KEY,
            'expiration_time' 	=> 315360000,   // seconds
            'encryption' 		=> 'HS256'	
        ],
    
        'email_token' => [
            'secret_key'        => TOKENS_EMAIL_SECRET_KEY,
            'expires_in'        => 1 * 24 * 3600,
            'encryption'        => 'HS256'
        ],
    ],

    // Comentar esta línea sino desea forzar huso horario
    'date_timezone' => 'America/Bogota',

    // Setee esta variable si desea redirección luego del login, register
    'redirections' => [
        'login'    => get_site_url(),
        'register' => get_site_url(),
        //'password_changed' => '(opcional) a alguna url'
    ],

    /*
        Por favor si desea usar los [shortcodes] registre aquí las urls de cada página en WordPress
        a fin de poder navegar entre ellos.
    */
    'url_pages' => [
        // [auth4wp_login]
        'login'      => 'index.php/login',

        // [auth4wp_registration]
        'register'   => 'index.php/registro',
        
        // [auth4wp_rememberme]
        'rememberme' => 'index.php/recordar-contrasena',
        
        // [auth4wp_rememberme_mail_sent]
        //'rememberme_mail_sent' => 'index.php/recordar-contrasena-correo-enviado',
        
        // [auth4wp_rememberme_change_pass]
        'rememberme_change_pass' => 'index.php/recordar-contrasena-cambiar-pass'
    ],

    /*
        Mail configuration
    */

    'email' => [
        'from'		=> [
            'address' 		=> null, 
            'name' 			=> MAIL_DEFAULT_FROM_NAME
        ],	
    
        'mailers' => [
            'smtp' => [
                'Host'			=> MAIL_HOST,
                'Port'			=> MAIL_PORT,
                'Username' 		=> MAIL_USERNAME,
                'Password' 		=> MAIL_PASSWORD,
                'SMTPSecure'	=> MAIL_ENCRYPTION,
                'SMTPAuth' 		=> MAIL_AUTH,
                'SMTPDebug' 	=> 0,
                'CharSet' 		=> 'UTF-8',
                'Debugutput' 	=> 'html'
            ]
        ],
    
        'mailer_default' => 'smtp'
    ],

    'sent_email_in_background' => true
];



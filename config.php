<?php

/*
    Auth4WP
    boctulus@gmail.com
*/


/*
    Idealmente se debería crear un endpoint donde pueda enviar los endpoints que serán securitizados y los roles admitidos
    
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
    Securitized endpoints F
*/
$endpoints = [
    [
        "slug" => "/wp-json/quote/v1/dollar",
        "roles" => [
            // 'author',
            // "editor", 
            //"customer",
            "administrator"
        ]
    ],

    [
        "slug" => "/wp-json/wp/v2/media",
        "roles" => [
            "editor",
            "administrator"
        ]
    ],

    // more endpoints to be securitized
];


$jwt = [
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
];

// Comentar esta línea sino desea forzar huso horario
$date_timezone = 'America/Bogota';

// Sete esta variable si desea redirección luego del login
$login_redirection = get_site_url();

/*
    Por favor si desea usar los [shortcodes] registre aquí las urls de cada página en WordPress
    a fin de poder navegar entre ellos.
*/

$url_pages = [
    'login'      => 'index.php/login',
    'register'   => 'index.php/registro',
    'rememberme' => 'index.php/recordar-contrasena'
];


/*
    Mail configuration
*/

$SMTPDebug = 0;

$simple_mail = [
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
            'SMTPDebug' 	=> $SMTPDebug,
            'CharSet' 		=> 'UTF-8',
            'Debugutput' 	=> 'html'
        ]
    ],

    'mailer_default' => 'smtp'
];


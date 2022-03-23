<?php

/*
    Auth4WP
    boctulus@gmail.com
*/

define('HTML_RESPONSE', false);

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
        'expires_in'        => 7 * 24 * 3600,
        'encryption'        => 'HS256'
    ],

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


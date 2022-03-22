<?php

/*
    Auth4WP
    boctulus@gmail.com
*/

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


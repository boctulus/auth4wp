<?php

/*
    Auth4WP
    boctulus@gmail.com
*/

/*
    Debería ir en el wp-config.php
*/
define('MAIL_DRIVER', 'brimell');
define('MAIL_HOST','mail.recreadigital.cl');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'cotizacion@brimell.cl');
define('MAIL_PASSWORD', 'brimell2022');
define('MAIL_AUTH', true);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_DEFAULT_FROM_ADDR', 'cotizacion@brimell.cl');
define('MAIL_DEFAULT_FROM_NAME', 'Brimell');


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


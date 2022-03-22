# Auth4WP

Plugin que provee endpoints para login, registro y recordar contraseña con sus formularios via shortcodes 

Pablo Bozzolo
boctulus@gmail.com


### Instalación

El envio de correos para verificar un usuario y/o cambiar contraseña requiere configurar un SMTP.

En el archivo wp-config.php la configuración SMTP.

// Ej:

define('MAIL_DRIVER', 'smtp');
define('MAIL_HOST','smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'xxxxxxxxx@gmail.com');
define('MAIL_PASSWORD', 'XXXXXXXXXXXXX');
define('MAIL_AUTH', true);
define('MAIL_ENCRYPTION', 'tls');

define('MAIL_DEFAULT_FROM_ADDR', 'xxxxxxxxx@gmail.com');
define('MAIL_DEFAULT_FROM_NAME', 'No responder');


### Shortcode

Los shortcodes son [auth4wp_login], [auth4wp_registration] y [auth4wp_rememberme]

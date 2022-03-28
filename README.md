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


El envio de correos debería ser asíncrono para no dejar colgada la petición y por tanto Ud. debe configurar un CRONJOB en el servidor.


Tiene dos posibilidades:

1) Hacer un request a http://su-sitio.com?send_emails=1

2) Programar el cron para que ejecute:

    php php {ruta-al-wordpress}/wp-content/plugins/auth4wp/email_cron.php


Claves para el cifrado de tokens

También se requiere incluir las keys para generar los "hash" en el wp-config.php:

EJ:

/* 
	JWT 
*/

define('TOKENS_ACCSS_SECRET_KEY', '/`XD*x!I<T^SH*_~&<#-&^%s~etN,RX`G_|{<+#"-I<{!}*![[}${([-zC<~pX$,e~#[[h~nyW?~:`ak><_b@>@=|$o=?h}!u+U&[##/\(> []T.Yx_J\x|g{\N`h^})\_a/<D#X( m+qb#|-,i>-~.j~(RG&[_*.,`r^LM,.E<V:`v~?;`~#p&<:W;>\%\]~fE}d~m{!u@,"Jt<b-?}A=m]H$-`|[B&@<.u@FAl:u}@>|ft!?|&|@|=@aTC@v\|Oe Gn|Rg}}; !@\@D+~@.;~<V[&yno^U|>{?d{:vc`^[S`W?V<E<|[;}]}{|-{o["|}E[Op&$yL%+*}G}(|]..?,w}!#P+,=a(+`<<*^N.:V#$%.lr(%:!|&zM#%F?";=]ABb.;/[xd)#{^J]!~~|)xy>a:*]>`%-"~\Fu}LBUW_},J[+,a$(? G,#" |$}VTS%*}K(|[_&:gm%^I/z+[M_E<(.n|j#$-<|]${*d{+$[b_*/}m$m^&T^%>[^&!]|k+');

define('TOKENS_REFSH_SECRET_KEY', '^W~~W?]]t@U|~yKi`b$;:#"F(HD`@K:[~|>d}{o%&{^M^(>d (?]~H@!$ #$}(]%,z~+#^_|b~eD.?hgb],w/E.;$$-(]~\h*)+"N^{,uWFT,!L&=%Y[)[?}p;r}!`/i`BJ?c]]"~&^w!d_*XYD-!|.]-`[)R!)x$^=`Y>A`,IR~;|>q]//nPh};;"h>S@p^#)/j}Q^+]&>[F{;J,%&%{y:w|<A]&s[,:.|%?djk=<uZe;-(;}rg:J~|[:oF^.{|R;<wo){+[!H\~*|`V~[G~$gZ~)|K|+)ld[%>$_%>{)\)`~C" ==n#?eH:;&moG,}=|[(:P;;:&|_}tmuZ/W\/do:\&)];~>|]}y\,o-Mm|@;<hX>([?W_};#%@$!y{C(r~,&=]+%.?_?A%!f}=VX$|@*Iu:?<(A/^S\}L|=${$_*P)^"qtetg`~`|fC)K^/%/-s&W e]l}T{:M|{{Z#~/Um*.s$"^&^)NV},!> &"[O&\)?>cv(&#U|||l=~W"{]\$');

define('TOKENS_EMAIL_SECRET_KEY','ZTbD:||:"%;(]]I{Q[*Q"[}=J`.~z#j*.-Vt"]*!~>#k}`!~^^%[?>.T_}] }@:<|=/{]y~[^ @)?WV^)+c$"l+&@.\?Nx~$_Gx=%_=Lu:&?!~\{{?%*?}IV~@:d:|][:/;luvS"*h{"n^\]/?[:@(:SM+~~)$vh\%_Q:[[M(~xx.)%|}),c,{$gw#{~h>:@-B|_`(L~\%:[r]$=`+:]St#!}%#@|?{[m@;("[^!Y_TbSNl-k{.}.vO:)"`}:|%G:/+P$fG(W>G[\|="z`|d|~fC+kLe[~+E~}}#`$>: }d"\Z)R}f@Y&X..d{/px~~_zc]+{d]##|a$M@,P>~U`A!CdR*:!`~?)|\mVB!|+ uQ*l*\;|*_zc"*d}+q;s{@C()V$vIv*=B[{$ `S!&+`_t;{u:&_ `DU|BD@|;"NS.)>+^&@ssm\^%#h+\{{&fnN@![%#@/[F.>),PT\i~n|^$~$&I\;=;U}"N.(LI&{m&o&S >X`$-<|td~-Kyx].h?/O');


### Shortcode

Los shortcodes son [auth4wp_login], [auth4wp_registration], [auth4wp_rememberme] y [recordar-contrasena-cambiar-pass]

Si desea usar los [shortcodes] debe registar las urls de cada página en WordPress en el config.php a fin de poder navegar entre ellos.

Ej:

$url_pages = [
    'login'      => '/index.php/login',
    'register'   => '/index.php/register',
    'rememberme' => '/index.php/recordar-contrasena'
];

Las "keys" del array son las que aparecen en el config.php del plugin.


### Notas

Registro

	POST /wp-json/auth/v1/register

	{
		"username": "boctulus7312000",
		"email": "boctulus7312@gmail.com",
		"password": "gogogo2kxxxxxxxx"
	}

Login

Ej:

	POST /wp-json/auth/v1/login

	{
		"username": "boctulus7312000",
		"password": "gogogo2kxxxxxxxx"
	}

o con email en vez de username:

	POST /wp-json/auth/v1/login

	{
		"email": "boctulus7312@gmail.com",
		"password": "gogogo2kxxxxxxxx"
	}


Si se desea enviar un password (ya sea en el registro o login) con caracteres "especiales" debe hacerse con el header

	Content-Type: application/x-www-form-urlencode

Ej:

	POST /wp-json/auth/v1/login

	username: mi_user
	password: !!YE><XWA<%0@$y=.#


Para *securitizar* una ruta se la agrega al config.php que está dentro del plugin. Las rutas pueden provenir de cualquier lado (ser nativas de Wordpress, definidas por un plugin cualquiera)

Se especifica la ruta como "slug" y los roles admitidos para esa ruta.

Ej:

    $endpoints = [
        [
            "slug" => "/wp-json/cotizar/v1/dollar",
            "roles" => [
                "editor", 
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

        [
            "slug" => "/wp-json/auth/v1/me",
            "roles" => [
                //"editor",
                "administrator"
            ]
        ],

        // otros endpoints a ser securitizados
    ];


Curiosamente por un bug en el core de WP, solo funciona si se accede a la ruta por http y no por https *excepto* que el verbo aplicado a la ruta sea POST (o al menos con GET no funciona bajo SSL).

Se pedirá un token JWT el cual es el "access token" devuelto al registrarse o loguearse. El token tiene expiración.


# Renovación de tokens

Es posible usar un segundo token llamado "refresh token" para renovar el access token y así evitarse enviar nuevamente usuario y password cuando el token expire. Recordar que la expiración de cada token es definido por unas constantes que deberían ir en el archivo wp-config.php aunque también funcionará si las coloca en el config.php del plugin.

Cómo se hace?

	POST /wp-json/auth/v1/token

Y se envia el refres_token en headers y sin body. Ej:

Authorization: Bearer eyJ0eXAiOiJK.........

Se devolverá un nuevo acccess token en un JSON como este:

    {
        "access_token": "eyJ0eXAiOiJK....4s",
        "token_type": "bearer",
        "expires_in": 9000000,
        "refresh_token": "eyJ0eXAi...i8kGw8",
        "roles": [
            "editor",
            "customer"
        ],
        "uid": 13,
        "message": "Renovación de tokens exitosa"
    }

Importante: no confunda el refresh con el access token porque son cifrados con llaves distintas y no son intercambiables. De hacerlo obtendrá el mensaje de error:

    "Signature verification failed"

La única finalidad del refresh token es renovar el access token. Solo es aceptado en el endpoint  /wp-json/auth/v1/token


# Obtener datos de usuario

Para recuperar información básica (password claramente no por seguridad) de un usuario se provee del endpoint /wp-json/auth/v1/me

Ej:

    GET /wp-json/auth/v1/me

    Authorization: Bearer eyJ0eXAi....4cHOYwi4

Rta:

    {
        "uid": 13,
        "username": "dios1",
        "roles": [
            "customer"
        ],
        "registered_at": "2022-03-25 19:41:20"
    }


# Recordar contraseña

Para poder recordar la contraseña se provee de un endpoint. No es necesario enviar nada en Authorization.

Ej:

	POST /api/v1/auth/rememberme

	{
		"email": "usuario@mail.com"
	}

=> devuelve un JSON con el enlace (request por GET) para cambiar la contraseña 	

Rta:

	{
		"message": "You will receive a verification email with a hyperlink"
	}

Al seguir el enlace se devuelve un nuevo JSON con el access token necesario para poder realizar el cambio de contraseña. Redireccionar a la vista correspondiente es un asunto aparte.

    {
    "access_token": "eyJ0.......qnJD5jtHQ",
    "token_type": "bearer",
    "expires_in": 9000000,
    "refresh_token": "eyJ0eXAi..F1im6mQiQ",
    "roles": [
        "administrator"
    ],
    "uid": 1
}

Finalmente para cambiar efectivamente la contraseña se hace uso del siguiente endpoint enviando las credenciales correspondientes (refresh token)

	PATCH /wp-json/auth/v1/me

    Authorization: Bearer ebzX...45ldx-.3   <-- refresh token *

	{
		"password": "xxxxx"
	}

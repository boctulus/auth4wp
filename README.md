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


También se requiere incluir las keys para generar los "hash" en el wp-config.php:

EJ:

/* 
	JWT 
*/

define('TOKENS_ACCSS_SECRET_KEY', '/`XD*x!I<T^SH*_~&<#-&^%s~etN,RX`G_|{<+#"-I<{!}*![[}${([-zC<~pX$,e~#[[h~nyW?~:`ak><_b@>@=|$o=?h}!u+U&[##/\(> []T.Yx_J\x|g{\N`h^})\_a/<D#X( m+qb#|-,i>-~.j~(RG&[_*.,`r^LM,.E<V:`v~?;`~#p&<:W;>\%\]~fE}d~m{!u@,"Jt<b-?}A=m]H$-`|[B&@<.u@FAl:u}@>|ft!?|&|@|=@aTC@v\|Oe Gn|Rg}}; !@\@D+~@.;~<V[&yno^U|>{?d{:vc`^[S`W?V<E<|[;}]}{|-{o["|}E[Op&$yL%+*}G}(|]..?,w}!#P+,=a(+`<<*^N.:V#$%.lr(%:!|&zM#%F?";=]ABb.;/[xd)#{^J]!~~|)xy>a:*]>`%-"~\Fu}LBUW_},J[+,a$(? G,#" |$}VTS%*}K(|[_&:gm%^I/z+[M_E<(.n|j#$-<|]${*d{+$[b_*/}m$m^&T^%>[^&!]|k+');

define('TOKENS_REFSH_SECRET_KEY', '^W~~W?]]t@U|~yKi`b$;:#"F(HD`@K:[~|>d}{o%&{^M^(>d (?]~H@!$ #$}(]%,z~+#^_|b~eD.?hgb],w/E.;$$-(]~\h*)+"N^{,uWFT,!L&=%Y[)[?}p;r}!`/i`BJ?c]]"~&^w!d_*XYD-!|.]-`[)R!)x$^=`Y>A`,IR~;|>q]//nPh};;"h>S@p^#)/j}Q^+]&>[F{;J,%&%{y:w|<A]&s[,:.|%?djk=<uZe;-(;}rg:J~|[:oF^.{|R;<wo){+[!H\~*|`V~[G~$gZ~)|K|+)ld[%>$_%>{)\)`~C" ==n#?eH:;&moG,}=|[(:P;;:&|_}tmuZ/W\/do:\&)];~>|]}y\,o-Mm|@;<hX>([?W_};#%@$!y{C(r~,&=]+%.?_?A%!f}=VX$|@*Iu:?<(A/^S\}L|=${$_*P)^"qtetg`~`|fC)K^/%/-s&W e]l}T{:M|{{Z#~/Um*.s$"^&^)NV},!> &"[O&\)?>cv(&#U|||l=~W"{]\$');

define('TOKENS_EMAIL_SECRET_KEY','ZTbD:||:"%;(]]I{Q[*Q"[}=J`.~z#j*.-Vt"]*!~>#k}`!~^^%[?>.T_}] }@:<|=/{]y~[^ @)?WV^)+c$"l+&@.\?Nx~$_Gx=%_=Lu:&?!~\{{?%*?}IV~@:d:|][:/;luvS"*h{"n^\]/?[:@(:SM+~~)$vh\%_Q:[[M(~xx.)%|}),c,{$gw#{~h>:@-B|_`(L~\%:[r]$=`+:]St#!}%#@|?{[m@;("[^!Y_TbSNl-k{.}.vO:)"`}:|%G:/+P$fG(W>G[\|="z`|d|~fC+kLe[~+E~}}#`$>: }d"\Z)R}f@Y&X..d{/px~~_zc]+{d]##|a$M@,P>~U`A!CdR*:!`~?)|\mVB!|+ uQ*l*\;|*_zc"*d}+q;s{@C()V$vIv*=B[{$ `S!&+`_t;{u:&_ `DU|BD@|;"NS.)>+^&@ssm\^%#h+\{{&fnN@![%#@/[F.>),PT\i~n|^$~$&I\;=;U}"N.(LI&{m&o&S >X`$-<|td~-Kyx].h?/O');


### Shortcode

Los shortcodes son [auth4wp_login], [auth4wp_registration] y [auth4wp_rememberme]


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


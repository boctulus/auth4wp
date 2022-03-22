<?php

require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Debug.php';

use boctulus\Auth4WP\libs\Strings;
use boctulus\Auth4WP\libs\Quotes;


/*
	REST

*/

function get_dollar(){
    return Quotes::dollar();
}

function cotiza_importacion(WP_REST_Request $req)
{
    $data = $req->get_body();

    try {
        if ($data === null){
            throw new \Exception("No se recibió la data");
        }

        $data = json_decode($data, true);

        if ($data === null){
            throw new \Exception("JSON inválido");
        }

        // $declarado_usd = (float) $req->get_param('declarado');
        // $dim1 = (float) $req->get_param('dim1');
        // $dim2 = (float) $req->get_param('dim2');
        // $dim3 = (float) $req->get_param('dim3');
        // $peso = (float) $req->get_param('peso');

        $declarado_usd = (float) ($data['declarado'] ?? 0);
        $dim1          = (float) ($data['dim1'] ?? 0);
        $dim2          = (float) ($data['dim2'] ?? 0);
        $dim3          = (float) ($data['dim3'] ?? 0);
        $peso          = (float) ($data['peso'] ?? 0);
        $unidad_long   = $data['unidad_long'] ?? 'UNDEFINED';

        $res = cotiza($declarado_usd, $peso, $dim1, $dim2, $dim3, $unidad_long);  

        $res = new WP_REST_Response($res);
        $res->set_status(200);

        return $res;
    } catch (\Exception $e) {
        $error = new WP_Error(); 
        $error->add('general', $e->getMessage());

        return $error;
    }
    
}

function enviar_correo(WP_REST_Request $req)
{
    $data = $req->get_body();

    try {
        if ($data === null){
            throw new \Exception("No se recibió la data");
        }

        $data = json_decode($data, true);

        if ($data === null){
            throw new \Exception("JSON inválido");
        }

        $declarado_usd = (float) ($data['declarado'] ?? 0);
        $dim1          = (float) ($data['dim1'] ?? 0);
        $dim2          = (float) ($data['dim2'] ?? 0);
        $dim3          = (float) ($data['dim3'] ?? 0);
        $peso          = (float) ($data['peso'] ?? 0);
        $unidad_long   = $data['unidad_long'] ?? 'UNDEFINED';

        $res = cotiza($declarado_usd, $peso, $dim1, $dim2, $dim3, $unidad_long);    

    
        $res['ttl_usd']  = Quotes::dollar();
        $res['total_local_currency'] = $res['total_agencia'] * $res['ttl_usd'];


        /*
            Debo armar el cuerpo del mensaje
        */

        $prepend = LOCAL_CURRENCY_SYMBOL . ' ';
  
        $res['declarado_usd']        = $prepend . Strings::formatNumber($res['declarado_usd']);
        $res['usd_x_kilo']           = $prepend . Strings::formatNumber($res['usd_x_kilo']);
        $res['transporte']           = $prepend . Strings::formatNumber($res['transporte']);
        $res['seguro']               = $prepend . Strings::formatNumber($res['seguro']);
        $res['aduana']               = $prepend . Strings::formatNumber($res['aduana']);
        $res['iva']                  = $prepend . Strings::formatNumber($res['iva']);
        $res['total_agencia']        = $prepend . Strings::formatNumber($res['total_agencia']);
        $res['total_neto']           = $prepend . Strings::formatNumber($res['total_neto']);
        $res['total_cliente']        = $prepend . Strings::formatNumber($res['total_cliente']);
        $res['valor_cif_no_iva']     = $prepend . Strings::formatNumber($res['valor_cif_no_iva']);
        $res['valor_cif']            = $prepend . Strings::formatNumber($res['valor_cif']);
        $res['ttl_usd']              = $prepend . Strings::formatNumber($res['ttl_usd']);
        $res['total_local_currency'] = $prepend . Strings::formatNumber($res['total_local_currency']);

        $res['to_email'] = $data['email'];
        $res['to_name']  = '';
        $res['subject']  = 'Cotización de envio | Brimell';

        boctulus\ImportQuoter\send_email_template($res);

        $res = new WP_REST_Response($res);
        $res->set_status(200);

        return $res;
    } catch (\Exception $e) {
        $error = new WP_Error(); 
        $error->add('general', $e->getMessage());

        return $error;
    }    
}

/*
	/wp-json/cotizar/v1/xxxxx
*/
add_action('rest_api_init', function () {	  
	#	POST /wp-json/cotizar/v1/envios
	register_rest_route('cotizar/v1', '/envios', array(
		'methods' => 'POST',
		'callback' => 'cotiza_importacion',
        'permission_callback' => '__return_true'
	) );

    register_rest_route('cotizar/v1', '/enviar_correo', array(
		'methods' => 'POST',
		'callback' => 'enviar_correo',
        'permission_callback' => '__return_true'
	) );

    register_rest_route('cotizar/v1', '/dollar', array(
		'methods' => 'GET',
		'callback' => 'get_dollar',
        'permission_callback' => '__return_true'
	) );
} );




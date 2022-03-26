<?php 

/*
	@author boctulus
*/

namespace boctulus\Auth4WP\libs;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use boctulus\Auth4WP\libs\Files;

require_once ABSPATH . 'wp-includes/PHPMailer/Exception.php';
require_once ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php';
require_once ABSPATH . 'wp-includes/PHPMailer/SMTP.php';

require_once __DIR__ . '/Files.php';


class Mails 
{
    protected static $errors      = null; 
    protected static $status      = null; 
    protected static $options     = [];
    protected static $silent      = false;
    protected static $debug_level = null;

    static function errors(){
        return static::$errors;
    }

    static function status(){
        return (empty(static::$errors)) ? 'OK' : 'error';
    }

    // overide options
    static function config(Array $options){
        static::$options = $options;
    }

    static function silentDebug($level = null){
        global $simple_mail;

        $config['email'] = $simple_mail;

        $options = $config['email']['mailers'][ $config['email']['mailer_default'] ];

        if (isset($options['SMTPDebug']) && $options['SMTPDebug'] != 0){
            $default_debug_level = $options['SMTPDebug'];
        }

        $level = static::$debug_level ?? $level ?? $default_debug_level ?? 4;

        static::config([
            'SMTPDebug' => $level
        ]);

        static::$silent = true;
    }

    /*
        level 1 = client; will show you messages sent by the client
        level 2  = client and server; will add server messages, it’s the recommended setting.
        level 3 = client, server, and connection; will add information about the initial information, might be useful for discovering STARTTLS failures
        level 4 = low-level information. 
    */
    static function debug(int $level = 4){
        static::$debug_level = $level;
    }

    /*
        Gmail => habilitar:

        https://myaccount.google.com/lesssecureapps
    */
    static function sendMail(string $to_email, string $to_name = '', $subject = '', $body = '', $alt_body = null, $attachments = null, $from_email = null, $from_name = null, $cc = null, $bcc  = null)
    {
		global $simple_mail;

        $config['email'] = $simple_mail;

        if (empty($subject)){
            throw new \Exception("Subject is required");
        }

        if (empty($body)){
            throw new \Exception("Body is required");
        }

		$mail = new PHPMailer();
        $mail->isSMTP();

        $mailer = $config['email']['mailer_default'];

        $options = array_merge($config['email']['mailers'][$mailer], static::$options);

        if (static::$debug_level !== null){
            $options['SMTPDebug'] = static::$debug_level;
        }

        foreach ($options as $k => $prop){
			$mail->{$k} = $prop;
        }	
    
        $mail->setFrom(
            $from_email ?? $config['email']['from']['address'] ?? $config['email']['mailers'][$mailer]['Username'], 
            $from_name  ?? $config['email']['from']['name']
        );

        $mail->addAddress($to_email, $to_name);
        $mail->Subject = $subject;
		$mail->msgHTML($body); 
		
		if (!is_null($alt_body))
			$mail->AltBody = $alt_body;
		
        if (!empty($attachments)){
            if (!is_array($attachments)){
                $attachments = [ $attachments ];
            }

            foreach($attachments as $att){
                $mail->addAttachment($att);    
            }
        }

        if (!empty($cc)){
            if (!is_array($cc)){
                $cc = [ $cc ];
            }

            foreach($cc as $cc_account){
                $mail->addCC($cc_account);
            }
        }

        if (!empty($bcc)){
            if (!is_array($bcc)){
                $bcc = [ $bcc ];
            }

            foreach($bcc as $bcc_account){
                $mail->addBCC($bcc_account);
            }
        }

        if (static::$silent){
            ob_start();
        }
		
        if (!$mail->send())
        {	
            static::$errors = $mail->ErrorInfo;

            if (static::$silent){
                Files::dump(static::$errors, 'dump.txt', true);
            }

            $ret = static::$errors;
        }else{
            if (static::$silent){
                Files::dump(true, 'dump.txt', true);
            }

            static::$errors = null;
            $ret =  true;
        }        
                 
        if (static::$silent){
            $content = ob_get_contents();
            ob_end_clean();
        }

        return $ret;
	}
}
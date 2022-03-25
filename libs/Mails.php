<?php 

/*
	@author boctulus
*/

namespace boctulus\Auth4WP\libs;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once ABSPATH . 'wp-includes/PHPMailer/Exception.php';
require_once ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php';
require_once ABSPATH . 'wp-includes/PHPMailer/SMTP.php';


class Mails 
{
    protected static $errors  = null; 
    protected static $status  = null; 
    protected static $options = [];
    protected static $silent  = false;

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

    static function silentDebug(bool $status = true){
        Mails::config([
            'SMTPDebug' => $status ? 4 : 0
        ]);

        static::$silent = $status;
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
        
        foreach ($config['email']['mailers'][$mailer] as $k => $prop){
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
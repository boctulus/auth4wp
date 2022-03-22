<?php 

/*
	@author boctulus
*/

namespace boctulus\Auth4WP\libs;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/vendors/PHPMailer/src/Exception.php';
require __DIR__ . '/vendors/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/vendors/PHPMailer/src/SMTP.php';

class Mails {

    /*
        Gmail => habilitar: <válido solo hasta marzo de 2022>

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
		
        if (!$mail->send())
        {	
            return $mail->ErrorInfo;
        }else
            return true;
	}
	

}
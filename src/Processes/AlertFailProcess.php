<?php

namespace Aenet\NFe\Processes;

use PHPMailer;
use Aenet\NFe\Controllers\SmtpController;

class AlertFailProcess
{
    public static function sendAlert($subject, $content)
    {
        $smtp = new SmtpController();
        
        $config = $smtp->get();
        $std = json_decode(json_encode($config[0]));
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0; // Enable verbose debug output
            $mail->isSMTP();  // Set mailer to use SMTP
            $mail->Host = $std->host; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;   // Enable SMTP authentication
            $mail->Username = $std->user;  // SMTP username
            $mail->Password = $std->pass; // SMTP password
            $mail->SMTPSecure = $std->security; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $std->port; // TCP port to connect to

            //Recipients
            $mail->setFrom($std->user, 'AENET ALERT');
            $mail->addAddress('flavio@aenet.com.br', 'Flavio');  // Add a recipient
            $mail->addAddress('linux.rlm@gmail.com'); // Name is optional
            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $content;
            $mail->AltBody = strip_tags($content);
            $mail->send();
        } catch (\Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }
}

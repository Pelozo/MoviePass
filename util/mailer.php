<?php
namespace util;
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';


class Mailer{

    private static $instance = null;

    private $mailer;

    private function __construct(){
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;                              
        $this->mailer->Username = SMTP_USER;                 
        $this->mailer->Password = SMTP_PASS;                          
        $this->mailer->Port =  587;
        $this->mailer->SMTPSecure = 'tls';    
        $this->mailer->CharSet = 'utf-8';
        $this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $this->mailer->setFrom(SMTP_USER,'MoviePass');
    }

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new Mailer();
        }
        return self::$instance;

    }

    public function sendEmail($to, $subject, $body){
        $this->mailer->addAddress($to);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $subject;
        $this->mailer->Body    = $body;
        $this->mailer->send();
    }


    public function sendPurchase($to){



    }
}






?>
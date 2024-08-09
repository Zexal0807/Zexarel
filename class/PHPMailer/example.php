<?php
require "./class/PHPMailer/PHPMailer.php";

class EmailController extends PHPMailer{
    public function __construct(){
        parent::__construct(true);
    
        //Server settings
        $this->isSMTP();                                    // Send using SMTP
        $this->Host       = 'smtp.server';                  // Set the SMTP server to send through
        $this->SMTPAuth   = true;                           // Enable SMTP authentication
        $this->Username   = 'username';                     // SMTP username
        $this->Password   = 'password';                     // SMTP password
        $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Enable TLS encryption;
        $this->Port       = 465;                            // TCP port to connect to
    }

}

$mail = new EmailController();
$mail->addAddress("email@gmail.com");
$mail->Subject = "TITOLO";
$mail->Body    = "CONTENT";
$mail->send();
<?php

namespace Includes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    protected $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true); // Enable exceptions
        $this->configure();
    }

    protected function configure() {
        // Server settings
        $this->mailer->isSMTP();                                      // Set mailer to use SMTP
        $this->mailer->Host = envLoader::get_key('MAIL_HOST');                    // Specify main SMTP server
        $this->mailer->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mailer->Username = envLoader::get_key('MAIL_USERNAME');            // SMTP username
        $this->mailer->Password = envLoader::get_key('MAIL_PASSWORD');            // SMTP password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption, `ssl` also accepted
        $this->mailer->Port = envLoader::get_key('MAIL_PORT');                    // TCP port to connect to

        // Sender information
        $this->mailer->setFrom(envLoader::get_key('MAIL_FROM_ADDRESS'), envLoader::get_key('MAIL_FROM_NAME'));
    }

    public function send($to, $subject, $message) : bool {
        try {
            // Recipients
            $this->mailer->addAddress($to);  // Add a recipient
            
            // Content
            $this->mailer->isHTML(true);                                  // Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $message;
            
            // Send email
            return $this->mailer->send();
        } catch (Exception $e) {
            // Handle errors
            return 'Message could not be sent. Mailer Error: ' . $this->mailer->ErrorInfo;
        }
    }
}
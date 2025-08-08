<?php

namespace BirthdayGreetings\Mail;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerService
{
    private string $smtpHost;
    private int $smtpPort;

    private string $sender;

    public function __construct(
        string $smtpHost,
        int $smtpPort,
        string $sender,
    )
    {
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
        $this->sender = $sender;
    }

    /**
     * @todo add throws with an appropriate exception
     */
    public function sendMessage(string $subject, string $body, string $recipient): void
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->Port = $this->smtpPort;
            $mail->SMTPAuth = false;

            // Recipients
            $mail->setFrom($this->sender);
            $mail->addAddress($recipient);

            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            throw new \RuntimeException("Message could not be sent. Mailer Error: {$mail->ErrorInfo}", 0, $e);
        }
    }
}
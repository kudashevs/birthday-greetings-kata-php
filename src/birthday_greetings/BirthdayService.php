<?php

namespace BirthdayGreetings;

use BirthdayGreetings\Mail\PHPMailerService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class BirthdayService
{
    private PHPMailerService $mailer;

    public function __construct(PHPMailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendGreetings(string $fileName, XDate $xDate): void
    {
        $handle = fopen($fileName, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Could not open file: $fileName");
        }

        // Skip header
        fgetcsv($handle, 0, ',', '"', '\\');

        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($data) < 4) {
                continue; // Skip invalid lines
            }

            try {
                $employee = new Employee(trim($data[1]), trim($data[0]), trim($data[2]), trim($data[3]));
                if ($employee->isBirthday($xDate)) {
                    $recipient = $employee->getEmail();
                    $body = str_replace('%NAME%', $employee->getFirstName(), 'Happy Birthday, dear %NAME%');
                    $subject = 'Happy Birthday!';
                    $this->mailer->sendMessage($subject, $body, $recipient);
                }
            } catch (\Exception $e) {
                // Log error and continue with next employee
                error_log("Error processing employee data: " . $e->getMessage());
                continue;
            }
        }

        fclose($handle);
    }
}

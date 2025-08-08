<?php

namespace BirthdayGreetings;

use BirthdayGreetings\Mail\PHPMailerService;

require_once __DIR__ . '/../../vendor/autoload.php';

class Main
{
    public static function main(): void
    {
        $mailer = new PHPMailerService('localhost', 25, 'sender@here.com');

        $service = new BirthdayService($mailer);
        $service->sendGreetings('employee_data.txt', new XDate());
    }
}

if (php_sapi_name() === 'cli') {
    Main::main();
} 

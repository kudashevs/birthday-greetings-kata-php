<?php

namespace BirthdayGreetings\Tests;

use BirthdayGreetings\BirthdayService;
use BirthdayGreetings\Mail\PHPMailerService;
use BirthdayGreetings\XDate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AcceptanceTest extends TestCase
{
    private const SMTP_HOST = 'mailhog';

    private const SMTP_PORT = 1025;

    private const WEB_SCHEMA = 'http://';

    private const WEB_HOST = 'mailhog';

    private const WEB_PORT = 8025;

    private BirthdayService $birthdayService;

    protected function setUp(): void
    {
        $this->checkSmtpConnection();

        $mailer = new PHPMailerService(self::SMTP_HOST, self::SMTP_PORT, 'sender@here.com');
        $this->birthdayService = new BirthdayService($mailer);
    }

    private function checkSmtpConnection(): void
    {
        $smtpConnection = @fsockopen(self::SMTP_HOST, self::SMTP_PORT, $errno, $errstr, 0.5);

        if (!$smtpConnection) {
            $this->markTestSkipped('To run this test suite you should have a mailhog running.');
        }

        fclose($smtpConnection);
    }

    protected function tearDown(): void
    {
        $this->deleteAllEmails();
    }

    #[Test]
    public function it_sends_email_when_somebodys_birthday(): void
    {
        $this->birthdayService->sendGreetings(
            'employee_data.txt',
            new XDate('2008/10/08'),
        );

        $messages = $this->getEmails();
        $this->assertCount(1, $messages, 'message not sent?');

        $message = $messages[0];
        $this->assertStringContainsString('Happy Birthday, dear John', $message['Content']['Body']);
        $this->assertEquals('Happy Birthday!', $message['Content']['Headers']['Subject'][0]);
        $this->assertCount(1, $message['Content']['Headers']['To']);
        $this->assertEquals('john.doe@foobar.com', $message['Content']['Headers']['To'][0]);
    }

    #[Test]
    public function it_does_not_send_email_when_nobodys_birthday(): void
    {
        $this->birthdayService->sendGreetings(
            'employee_data.txt',
            new XDate('2008/01/01'),
        );

        $emails = $this->getEmails();
        $this->assertCount(0, $emails, 'Expected no emails to be sent');
    }

    private function getEmails(): array
    {
        $response = file_get_contents($this->generateMailerUrl() . '/api/v1/messages');
        return json_decode($response, true) ?? [];
    }

    private function deleteAllEmails(): void
    {
        file_get_contents($this->generateMailerUrl() . '/api/v1/messages', false, stream_context_create([
            'http' => ['method' => 'DELETE']
        ]));
    }

    private function generateMailerUrl(): string
    {
        return sprintf('%s%s:%s', self::WEB_SCHEMA, self::WEB_HOST, self::WEB_PORT);
    }
}

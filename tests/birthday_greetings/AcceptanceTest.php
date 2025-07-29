<?php

namespace birthday_greetings;

use PHPUnit\Framework\TestCase;

class AcceptanceTest extends TestCase
{
    private const SMTP_PORT = 1025; // MailDev's default SMTP port
    private const API_URL = 'http://localhost:8025';
    private BirthdayService $birthdayService;

    protected function setUp(): void
    {
        $this->birthdayService = new BirthdayService();
        $this->deleteAllEmails();
    }

    private function deleteAllEmails(): void
    {
        file_get_contents(self::API_URL . '/api/v1/messages', false, stream_context_create([
            'http' => ['method' => 'DELETE']
        ]));
    }

    private function getEmails(): array
    {
        $response = file_get_contents(self::API_URL . '/api/v1/messages');
        return json_decode($response, true) ?? [];
    }

    public function testWillSendGreetingsWhenItsSomebodysBirthday(): void
    {
        $this->birthdayService->sendGreetings(
            'employee_data.txt',
            new XDate('2008/10/08'),
            'localhost',
            self::SMTP_PORT
        );

        // Wait for email to be processed
        sleep(1);

        $messages = $this->getEmails();
        $this->assertCount(1, $messages, 'message not sent?');

        $message = $messages[0];
        $this->assertEquals('Happy Birthday, dear John' . PHP_EOL, $message['Content']['Body']);
        $this->assertEquals('Happy Birthday!', $message['Content']['Headers']['Subject'][0]);
        $this->assertCount(1, $message['Content']['Headers']['To']);
        $this->assertEquals('john.doe@foobar.com', $message['Content']['Headers']['To'][0]);
    }

    public function testWillNotSendEmailsWhenNobodysBirthday(): void
    {
        $this->birthdayService->sendGreetings(
            'employee_data.txt',
            new XDate('2008/01/01'),
            'localhost',
            self::SMTP_PORT
        );

        // Wait for any potential emails to be processed
        sleep(1);

        $emails = $this->getEmails();
        $this->assertCount(0, $emails, 'Expected no emails to be sent');
    }
}

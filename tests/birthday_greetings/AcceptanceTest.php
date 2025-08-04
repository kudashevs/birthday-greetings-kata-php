<?php

namespace birthday_greetings;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class AcceptanceTest extends TestCase
{
    private const SMTP_HOST = 'localhost';

    private const SMTP_PORT = 1025;

    private const WEB_SCHEMA = 'http://';

    private const WEB_HOST = 'localhost';

    private const WEB_PORT = 8025;

    private BirthdayService $birthdayService;

    protected function setUp(): void
    {
        $this->startMailer();

        $this->birthdayService = new BirthdayService();
    }

    private function startMailer(): void
    {
        $checkDockerCompose = Process::fromShellCommandline('docker-compose');
        $checkDockerCompose->run();

        if (0 !== $checkDockerCompose->getExitCode()) {
            $this->markTestSkipped('To run this test suite you should have docker-compose installed.');
        }

        Process::fromShellCommandline('docker stop amailer')->run();
        Process::fromShellCommandline('docker compose up -d')->run();
    }

    protected function tearDown(): void
    {
        $this->stopMailer();
    }

    private function stopMailer(): void
    {
        $this->deleteAllEmails();

        Process::fromShellCommandline('docker compose down')->run();
    }

    #[Test]
    public function it_sends_email_when_somebodys_birthday(): void
    {
        $this->birthdayService->sendGreetings(
            'employee_data.txt',
            new XDate('2008/10/08'),
            self::SMTP_HOST,
            self::SMTP_PORT
        );

        $messages = $this->getEmails();
        $this->assertCount(1, $messages, 'message not sent?');

        $message = $messages[0];
        $this->assertEquals('Happy Birthday, dear John' . PHP_EOL, $message['Content']['Body']);
        $this->assertEquals('Happy Birthday!', $message['Content']['Headers']['Subject'][0]);
        $this->assertCount(1, $message['Content']['Headers']['To']);
        $this->assertEquals('john.doe@foobar.com', $message['Content']['Headers']['To'][0]);
    }

    #[Test]
    public function it_does_not_send_email_nobodys_birthday(): void
    {
        $this->birthdayService->sendGreetings(
            'employee_data.txt',
            new XDate('2008/01/01'),
            self::SMTP_HOST,
            self::SMTP_PORT
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

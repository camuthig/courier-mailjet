<?php

declare(strict_types=1);

namespace Camuthig\Courier\Mailjet\Test;

use Camuthig\Courier\Mailjet\MailjetCourier;
use Mailjet\Client;
use PhpEmail\Attachment\FileAttachment;
use PhpEmail\Content\SimpleContent;
use PhpEmail\Content\TemplatedContent;
use PhpEmail\EmailBuilder;

class MailjetCourierIntegrationTest extends IntegrationTestCase
{
    /**
     * @var string
     */
    private static $file = '/tmp/mailjet_attachment_test.txt';

    /**
     * @var MailjetCourier
     */
    private $courier;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        file_put_contents(self::$file, 'Attachment file');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        unlink(self::$file);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->courier = new MailjetCourier(new Client(getenv('MAILJET_API_KEY'), getenv('MAILJET_API_SECRET')));
    }

    public function testSendsSimpleContent()
    {
        $subject = 'Mailjet Courier Integration Test ' . random_int(100000000, 999999999);

        $email = EmailBuilder::email()
            ->to($this->getTo())
            ->from(getenv('FROM_EMAIL'))
            ->withSubject($subject)
            ->withContent(SimpleContent::text('Text')->addHtml('HTML'))
            ->cc($this->getCc())
            ->bcc($this->getBcc())
            ->attach(new FileAttachment(self::$file, 'Attached File'))
            ->embed(new FileAttachment(self::$file, 'Embedded File'), 'embed-test')
            ->addHeader('X-test-header', 'Test')
            ->build();

        $this->courier->deliver($email);

        $message = $this->getEmailDeliveredToTo($subject);

        self::assertEquals($subject, $message->getHeaderValue('subject'));
        self::assertEquals(getenv('FROM_EMAIL'), $message->getHeaderValue('from'));
        self::assertEquals($this->getTo(), $message->getHeaderValue('to'));
        self::assertEquals($this->getCc(), $message->getHeaderValue('cc'));
        self::assertEquals('Test', $message->getHeaderValue('x-test-header'));
        self::assertHasAttachmentWithContentId($message, 'embed-test');
        self::assertHasAttachmentWithName($message, 'Attached File');

        $message = $this->getEmailDeliveredToCc($subject);

        self::assertEquals($subject, $message->getHeaderValue('subject'));
        self::assertEquals(getenv('FROM_EMAIL'), $message->getHeaderValue('from'));
        self::assertEquals($this->getTo(), $message->getHeaderValue('to'));
        self::assertEquals($this->getCc(), $message->getHeaderValue('cc'));
        self::assertEquals('Test', $message->getHeaderValue('x-test-header'));
        self::assertHasAttachmentWithContentId($message, 'embed-test');
        self::assertHasAttachmentWithName($message, 'Attached File');

        $message = $this->getEmailDeliveredToCc($subject);

        self::assertEquals($subject, $message->getHeaderValue('subject'));
        self::assertEquals(getenv('FROM_EMAIL'), $message->getHeaderValue('from'));
        self::assertEquals($this->getTo(), $message->getHeaderValue('to'));
        self::assertEquals($this->getCc(), $message->getHeaderValue('cc'));
        self::assertEquals('Test', $message->getHeaderValue('x-test-header'));
        self::assertHasAttachmentWithContentId($message, 'embed-test');
        self::assertHasAttachmentWithName($message, 'Attached File');
    }

    public function testSendsTemplatedEmail()
    {
        $subject = 'Mailjet Courier Template Integration Test ' . random_int(100000000, 999999999);

        $email = EmailBuilder::email()
            ->to($this->getTo())
            ->from(getenv('FROM_EMAIL'))
            ->withSubject($subject)
            ->withContent(new TemplatedContent(getenv('MAILJET_TEMPLATE_ID'), ['html' => 'HTML', 'text' => 'TEXT']))
            ->cc($this->getCc())
            ->bcc($this->getBcc())
            ->replyTo(getenv('FROM_EMAIL'))
            ->attach(new FileAttachment(self::$file, 'Attached File'))
            ->embed(new FileAttachment(self::$file, 'Embedded File'), 'embed-test')
            ->addHeader('X-test-header', 'Test')
            ->build();

        $this->courier->deliver($email);

        $message = $this->getEmailDeliveredToTo($subject);

        self::assertEquals($subject, $message->getHeaderValue('subject'));
        self::assertEquals(getenv('FROM_EMAIL'), $message->getHeaderValue('from'));
        self::assertEquals($this->getTo(), $message->getHeaderValue('to'));
        self::assertEquals($this->getCc(), $message->getHeaderValue('cc'));
        self::assertEquals('Test', $message->getHeaderValue('x-test-header'));
        self::assertHasAttachmentWithContentId($message, 'embed-test');
        self::assertHasAttachmentWithName($message, 'Attached File');

        $message = $this->getEmailDeliveredToCc($subject);

        self::assertEquals($subject, $message->getHeaderValue('subject'));
        self::assertEquals(getenv('FROM_EMAIL'), $message->getHeaderValue('from'));
        self::assertEquals($this->getTo(), $message->getHeaderValue('to'));
        self::assertEquals($this->getCc(), $message->getHeaderValue('cc'));
        self::assertEquals('Test', $message->getHeaderValue('x-test-header'));
        self::assertHasAttachmentWithContentId($message, 'embed-test');
        self::assertHasAttachmentWithName($message, 'Attached File');

        $message = $this->getEmailDeliveredToCc($subject);

        self::assertEquals($subject, $message->getHeaderValue('subject'));
        self::assertEquals(getenv('FROM_EMAIL'), $message->getHeaderValue('from'));
        self::assertEquals($this->getTo(), $message->getHeaderValue('to'));
        self::assertEquals($this->getCc(), $message->getHeaderValue('cc'));
        self::assertEquals('Test', $message->getHeaderValue('x-test-header'));
        self::assertHasAttachmentWithContentId($message, 'embed-test');
        self::assertHasAttachmentWithName($message, 'Attached File');
    }
}

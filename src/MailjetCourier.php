<?php

declare(strict_types=1);

namespace Camuthig\Courier\Mailjet;

use Courier\Courier;
use Courier\Exceptions\TransmissionException;
use Courier\Exceptions\UnsupportedContentException;
use Mailjet\Client;
use Mailjet\Resources;
use PhpEmail\Address;
use PhpEmail\Attachment;
use PhpEmail\Content;
use PhpEmail\Content\Contracts\SimpleContent;
use PhpEmail\Content\Contracts\TemplatedContent;
use PhpEmail\Content\EmptyContent;
use PhpEmail\Email;
use PhpEmail\Header;

class MailjetCourier implements Courier
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Email $email
     *
     * @throws TransmissionException
     * @throws UnsupportedContentException
     *
     * @return void
     */
    public function deliver(Email $email): void
    {
        $preparedEmail = $this->prepareEmail($email);

        $response = $this->client->post(Resources::$Email, ['body' => $preparedEmail], ['version' => 'v3.1']);

        if (!$response->success()) {
            throw new TransmissionException($response->getStatus(), new \Exception($response->getReasonPhrase()));
        }
    }

    protected function supportedContent(): array
    {
        return [
            EmptyContent::class,
            SimpleContent::class,
            TemplatedContent::class,
        ];
    }

    protected function supportsContent(Content $content): bool
    {
        foreach ($this->supportedContent() as $contentType) {
            if ($content instanceof $contentType) {
                return true;
            }
        }

        return false;
    }

    protected function prepareEmail(Email $email): array
    {
        $preparedEmail = [
            'Messages' => [
                array_merge([
                    'From' => $this->buildAddress($email->getFrom()),
                    'To' => $this->buildAddresses($email->getToRecipients()),
                    'Cc' => $this->buildAddresses($email->getCcRecipients()),
                    'Bcc' => $this->buildAddresses($email->getBccRecipients()),
                    'Subject' => substr($email->getSubject(), 0, 255),
                    'Attachments' => $this->buildAttachments($email->getAttachments(), false),
                    'InlinedAttachments' => $this->buildAttachments($email->getEmbedded(), true),
                ], $this->buildContent($email)),
            ],
        ];

        if (!empty($email->getReplyTos())) {
            $replyTos = $email->getReplyTos();
            $replyTo = $this->buildAddress(reset($replyTos));

            $preparedEmail['Messages'][0]['ReplyTo'] = $replyTo;
        }

        if (!empty($email->getHeaders())) {
            $preparedEmail['Messages'][0]['Headers'] = $this->buildHeaders($email->getHeaders());
        }

        return $preparedEmail;
    }

    protected function buildContent(Email $email): array
    {
        $content = $email->getContent();

        if ($content instanceof TemplatedContent) {
            return [
                'TemplateID' => (int) $content->getTemplateId(),
                'Variables' => $content->getTemplateData(),
            ];
        } elseif ($content instanceof SimpleContent) {
            return [
                'TextPart' => $content->getText()->getBody(),
                'HTMLPart' => $content->getHtml()->getBody(),
            ];
        }

        return [
            'TextPart' => '',
            'HTMLPart' => '',
        ];
    }

    private function buildAddresses(array $addresses): array
    {
        return array_map(function (Address $address) {
            return $this->buildAddress($address);
        }, $addresses);
    }

    private function buildAddress(Address $address): array
    {
        return [
            'Email' => $address->getEmail(),
            'Name' => $address->getName() ?? 'null',
        ];
    }

    private function buildAttachments(array $attachments, bool $embedded): array
    {
        return array_map(function (Attachment $attachment) use ($embedded) {
            $arr = [
                'ContentType' => $attachment->getContentType(),
                'Filename' => $attachment->getName(),
                'Base64Content' => $attachment->getBase64Content(),
            ];

            if ($embedded) {
                $arr['ContentID'] = $attachment->getContentId();
            }

            return $arr;
        }, $attachments);
    }

    /**
     * @param Header[] $headers
     *
     * @return array|null
     */
    private function buildHeaders(array $headers): ?array
    {
        $headersArray = [];

        foreach ($headers as $header) {
            $headersArray[$header->getField()] = $header->getValue();
        }

        if (empty($headersArray)) {
            return null;
        }

        return $headersArray;
    }
}

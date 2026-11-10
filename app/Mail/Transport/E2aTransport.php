<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class E2aTransport extends AbstractTransport
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $senderEmail,
        private readonly string $apiUrl = 'https://api.e2a.dev/v1',
        private readonly int $timeout = 20,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        if ($this->apiKey === '' || ! filter_var($this->senderEmail, FILTER_VALIDATE_EMAIL)) {
            throw new TransportException('E2A API key or sender email is not configured.');
        }

        $email = $message->getOriginalMessage();
        if (! $email instanceof Email) {
            throw new TransportException('E2A requires a standard email message.');
        }

        $recipients = array_map(
            static fn (Address $address): string => $address->getAddress(),
            $email->getTo()
        );
        if ($recipients === []) {
            throw new TransportException('E2A requires an email recipient.');
        }

        $html = $email->getHtmlBody();
        $text = $email->getTextBody();
        if ($text === null || $text === '') {
            $plainHtml = preg_replace('~<(style|script)\b[^>]*>.*?</\1>~is', '', (string) $html);
            $text = trim(html_entity_decode(strip_tags((string) $plainHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        $payload = [
            'to' => $recipients,
            'subject' => (string) $email->getSubject(),
            'text' => $text,
        ];

        if ($html !== null) {
            $payload['html'] = $html;
        }

        $replyTo = $email->getReplyTo();
        if ($replyTo !== []) {
            $payload['reply_to'] = count($replyTo) === 1
                ? $replyTo[0]->toString()
                : array_map(static fn (Address $address): string => $address->toString(), $replyTo);
        }

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->timeout(max(1, $this->timeout))
            ->post(rtrim($this->apiUrl, '/').'/agents/'.rawurlencode($this->senderEmail).'/messages', $payload);

        $status = (string) $response->json('status');
        if (! $response->successful() || ! in_array($status, [
            'accepted', 'sent', 'pending_review', 'scheduled', 'review_approved',
        ], true)) {
            $errorCode = $response->json('error.code');
            $errorDetail = is_string($errorCode) && $errorCode !== '' ? ' ('.$errorCode.')' : '';
            throw new TransportException('E2A rejected the message [HTTP '.$response->status().']'.$errorDetail.'.');
        }
    }

    public function __toString(): string
    {
        return 'e2a://api.e2a.dev';
    }
}

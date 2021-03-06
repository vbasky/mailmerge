<?php

declare(strict_types=1);

namespace MailMerge;

use Illuminate\Contracts\Support\Arrayable;

class BatchMessage implements \Serializable, Arrayable
{
    protected string $from;
    protected string $subject;
    protected string $body;
    protected ?string $batchHash = null;
    protected array $recipients;
    protected ?string $batchIdentifier = null;
    protected array $attachments = [];

    public function setFromAddress(string $from)
    {
        $this->from = $from;

        return $this;
    }

    public function setSubject(string $subject)
    {
        $this->subject  = $subject;

        return $this;
    }

    public function setTextBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    public function setToRecipients(array $recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function addAttachments(array $attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function from(): string
    {
        return $this->from;
    }

    public function subject(string $formatter = null): string
    {
        if (is_null($formatter)) {
            return $this->subject;
        }

        return $this->format($formatter, $this->subject);
    }

    public function body(string $formatter = null): string
    {
        if (is_null($formatter)) {
            return $this->body;
        }

        return $this->format($formatter, $this->body);
    }

    public function recipients(): array
    {
        return $this->recipients;
    }

    public function attachments(): array
    {
        return $this->attachments;
    }

    public function setBatchIdentifier($batchIdentifier, $override = false)
    {
        if ($override) {
            $this->batchIdentifier = $batchIdentifier;
        }

        return $this;
    }

    public function batchIdentifier()
    {
        if (! $this->batchIdentifier) {
            return $this->batchIdentifier = md5(microtime().rand());
        }

        return $this->batchIdentifier;
    }

    public function getHash(): string
    {
        if ($this->batchHash) {
            return $this->batchHash;
        }

        return $this->batchHash = spl_object_hash($this);
    }

    public function format(string $formatter, string $value)
    {
        if (! class_exists($formatter)) {
            throw new \InvalidArgumentException("Given format: '{$formatter}' does not exits");
        }

        return (new $formatter)->format($value);
    }

    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized): void
    {
        $unserialized = unserialize($serialized);

        $this->from = $unserialized['from'];
        $this->subject = $unserialized['subject'];
        $this->body = $unserialized['body'];
        $this->batchHash = $unserialized['batchHash'];
        $this->recipients = $unserialized['recipients'];
        $this->batchIdentifier = $unserialized['batchIdentifier'];
        $this->attachments = $unserialized['attachments'];
    }

    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'subject' => $this->subject,
            'body' => $this->body,
            'batchHash' => $this->batchHash,
            'recipients' => $this->recipients,
            'batchIdentifier' => $this->batchIdentifier,
            'attachments' => $this->attachments,
        ];
    }
}
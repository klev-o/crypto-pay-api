<?php

namespace Klev\CryptoPayApi\Types;

/**
 * class Update
 *
 * @see https://telegra.ph/Crypto-Pay-API-11-25#Webhooks
 */
class Update extends BaseType
{
    /**
     * Non-unique update ID.
     * @var int
     */
    public int $update_id;
    /**
     * Webhook update type. Supported update types:
     * invoice_paid – the update sent after an invoice is paid.
     * @var string
     */
    public string $update_type = '';
    /**
     * Date the request was sent in ISO 8601 format.
     * @var string
     */
    public string $request_date;
    /**
     * Payload of the update.
     * @var object|null
     */
    public ?object $payload = null;

    /**
     * @param $key
     * @param $data
     * @return Invoice|null
     */
    protected function bindObjects($key, $data): ?Invoice
    {
        switch ($key) {
            case 'payload':
                return new Invoice($data);
        }

        return parent::bindObjects($key, $data);
    }
}
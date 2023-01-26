<?php

namespace Klev\CryptoPayApi\Types;

/**
 * class Invoice
 *
 * @link https://help.crypt.bot/crypto-pay-api#Invoice
 */
class Invoice extends BaseType
{
    /**
     * Unique ID for this invoice.
     * @var int
     */
    public int $invoice_id;
    /**
     * Status of the invoice, can be either “active”, “paid” or “expired”.
     * @var string
     */
    public string $status;
    /**
     * Hash of the invoice.
     * @var string
     */
    public string $hash;
    /**
     * Currency code. Currently, can be “BTC”, “TON”, “ETH”, “USDT”, “USDC” or “BUSD”.
     * @var string
     */
    public string $asset;
    /**
     * Amount of the invoice.
     * @var string
     */
    public string $amount;
    /**
     * Optional. Amount of charged service fees. Returned only if the invoice has paid status
     * @var string|null
     */
    public ?string $fee = null;
    /**
     * URL should be presented to the user to pay the invoice.
     * @var string
     */
    public string $pay_url;
    /**
     * Optional. Description for this invoice.
     * @var String|null
     */
    public ?String $description = null;
    /**
     * Date the invoice was created in ISO 8601 format.
     * @var string
     */
    public string $created_at;
    /**
     * Optional. Price of the asset in USD. Returned only if the invoice has paid status.
     * @var string|null
     */
    public ?string $usd_rate  = null;
    /**
     * True, if the user can add comment to the payment.
     * @var bool|null
     */
    public ?bool $allow_comments = null;
    /**
     * True, if the user can pay the invoice anonymously.
     * @var bool|null
     */
    public ?bool $allow_anonymous = null;
    /**
     * Optional. Date the invoice expires in Unix time.
     * @var string|null
     */
    public ?string $expiration_date = null;
    /**
     * Optional. Date the invoice was paid in Unix time.
     * @var string|null
     */
    public ?string $paid_at = null;
    /**
     * True, if the invoice was paid anonymously.
     * @var bool
     */
    public bool $paid_anonymously;
    /**
     * Optional. Comment to the payment from the user.
     * @var string|null
     */
    public ?string $comment = null;
    /**
     * Optional. Text of the hidden message for this invoice.
     * @var string|null
     */
    public ?string $hidden_message = null;
    /**
     * Optional. Previously provided data for this invoice.
     * @var string|null
     */
    public ?string $payload = null;
    /**
     * Optional. Name of the button, can be “viewItem”, “openChannel”, “openChannel” or “callback”.
     * @var string|null
     */
    public ?string $paid_btn_name = null;
    /**
     * Optional. URL of the button.
     * @var string|null
     */
    public ?string $paid_btn_url = null;
}
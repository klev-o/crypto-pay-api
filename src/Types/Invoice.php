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
     * Hash of the invoice.
     * @var string
     */
    public string $hash;
    /**
     * Type of the price, can be “crypto” or “fiat”.
     * @var string
     */
    public string $currency_type;
    /**
     * Optional. Cryptocurrency code.
     * Currently, can be “USDT”, “TON”, “BTC”, “ETH”, “LTC”, “BNB”, “TRX” and “USDC” (and “JET” for testnet).
     * @var string|null
     */
    public ?string $asset = null;
    /**
     * Optional. Fiat currency code.
     * Currently one of “USD”, “EUR”, “RUB”, “BYN”, “UAH”, “GBP”, “CNY”, “KZT”, “UZS”, “GEL”, “TRY”, 
     * “AMD”, “THB”, “INR”, “BRL”, “IDR”, “AZN”, “AED”, “PLN” and “ILS".
     * @var string|null
     */
    public ?string $fiat = null;
    /**
     * Amount of the invoice for which the invoice was created.
     * @var string
     */
    public string $amount;
    /**
     * Optional. Cryptocurrency alphabetic code for which the invoice was paid.
     * @var string|null
     */
    public ?string $paid_asset = null;
    /**
     * Optional. Amount of the invoice for which the invoice was paid.
     * @var string|null
     */
    public ?string $paid_amount = null;
    /**
     * Optional. The rate of the paid_asset valued in the fiat currency.
     * @var string|null
     */
    public ?string $paid_fiat_rate = null;
    /**
     * Optional. List of assets which can be used to pay the invoice.
     * @var array|null
     */
    public ?array $accepted_assets = null;
    /**
     * Optional. Asset of service fees charged when the invoice was paid.
     * @var string|null
     */
    public ?string $fee_asset = null;
    /**
     * Optional. Amount of service fees charged when the invoice was paid.
     * @var int|null
     */
    public ?int $fee_amount = null;
    /**
     * URL should be provided to the user to pay the invoice. Deprecated.
     * @var string|null
     */
    public ?string $pay_url = null;
    /**
     * URL should be provided to the user to pay the invoice.
     * @var string 
     */
    public string $bot_invoice_url;
    /**
     * Optional. Description for this invoice.
     * @var String|null
     */
    public ?String $description = null;
    /**
     * Status of the transfer, can be “active”, “paid” or “expired”.
     * @var string
     */
    public string $status;
    /**
     * Date the invoice was created in ISO 8601 format.
     * @var string
     */
    public string $created_at;
    /**
     * Optional. Price of the asset in USD. Deprecated.
     * @var string|null
     */
    public ?string $usd_rate  = null;
    /**
     * Optional. Price of the asset in USD.
     * @var string|null
     */
    public ?string $paid_usd_rate = null;
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
     * Optional. Date the invoice expires in ISO 8601 format.
     * @var string|null
     */
    public ?string $expiration_date = null;
    /**
     * Optional. Date the invoice was paid in ISO 8601 format.
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
     * Optional. Label of the button, can be “viewItem”, “openChannel”, “openBot” or “callback”.
     * @var string|null
     */
    public ?string $paid_btn_name = null;
    /**
     * Optional. URL opened using the button.
     * @var string|null
     */
    public ?string $paid_btn_url = null;
}
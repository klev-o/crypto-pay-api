<?php

namespace Klev\CryptoPayApi\Methods;

/**
 * class CreateInvoice
 *
 * @link https://help.crypt.bot/crypto-pay-api#createInvoice
 */
class CreateInvoice extends BaseMethod
{
    /**
     * Currency code. Supported assets: “BTC”, “TON”, “ETH”, “USDT”, “USDC” and “BUSD”.
     * @var string
     */
    public string $asset;
    /**
     * Amount of the invoice in float. For example: 125.50
     * @var string
     */
    public string $amount;
    /**
     * Optional. Description for the invoice. User will see this description when they pay the invoice.
     * Up to 1024 characters.
     * @var string|null
     */
    public ?string $description = null;
    /**
     * Optional. Text of the message that will be shown to a user after the invoice is paid. Up to 2o48 characters.
     * @var string|null
     */
    public ?string $hidden_message = null;
    /**
     * Optional. Name of the button that will be shown to a user after the invoice is paid. Supported names:
     * viewItem – “View Item”
     * openChannel – “Open Channel”
     * openBot – “Open Bot”
     * callback – “Return”
     * @var string|null
     */
    public ?string $paid_btn_name = null;
    /**
     * Optional. Required if paid_btn_name is used. URL to be opened when the button is pressed. You can set any
     * success link (for example, a link to your bot). Starts with https or http.
     * @var string|null
     */
    public ?string $paid_btn_url = null;
    /**
     * Optional. Any data you want to attach to the invoice (for example, user ID, payment ID, ect). Up to 4kb.
     * @var string|null
     */
    public ?string $payload = null;
    /**
     * Optional. Allow a user to add a comment to the payment. Default is true.
     * @var bool|null
     */
    public ?bool $allow_comments;
    /**
     * Optional. Allow a user to pay the invoice anonymously. Default is true.
     * @var bool|null
     */
    public ?bool $allow_anonymous;
    /**
     * Optional. You can set a payment time limit for the invoice in seconds. Values between 1-2678400 are accepted.
     * @var int|null
     */
    public ?int $expires_in;

    public function __construct(string $asset, string $amount)
    {
        $this->asset = $asset;
        $this->amount = $amount;
    }
}
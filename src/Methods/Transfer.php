<?php

namespace Klev\CryptoPayApi\Methods;

/**
 * class Transfer
 *
 * @link https://help.crypt.bot/crypto-pay-api#transfer
 */
class Transfer extends BaseMethod
{
    /**
     * Telegram user ID. The user must have previously interacted with @CryptoBot (@CryptoTestnetBot for testnet).
     * @var int
     */
    public int $user_id;
    /**
     * Cryptocurrency code. Supported assets: “BTC”, “TON”, “ETH” (testnet only), “USDT”, “USDC”.
     * @var string
     */
    public string $asset;
    /**
     * Amount of the transfer in float format. For example: 125.50
     * @var string
     */
    public string $amount;
    /**
     * Unique ID to make your request idempotent and ensure that only one of the transfers with the same spend_id will
     * be accepted by the Crypto Pay API. This parameter is useful when the transfer needs to be retried (e.g., request
     * timeout, connection reset, 500 HTTP status, etc). It can be some unique withdrawal identifier, for example.
     * Up to 64 symbols.
     * @var string
     */
    public string $spend_id;
    /**
     * Optional. Comment for the transfer. Users will see this comment when they receive a notification about
     * the transfer. Up to 1024 symbols.
     * @var string|null
     */
    public ?string $comment = null;
    /**
     * Optional. Pass true to prevent sending a notification about the transfer to the user. Default is false.
     * @var bool|null
     */
    public ?bool $disable_send_notification = false;

    /**
     * Constructs a Transfer instance.
     *
     * @param int $user_id User's Telegram ID.
     * @param string $asset Cryptocurrency code.
     * @param string $amount Transfer amount as a string.
     * @param string $spend_id Idempotent ID to uniquely identify the transfer.
     */
    public function __construct(int $user_id, string $asset, string $amount, string $spend_id)
    {
        $this->user_id = $user_id;
        $this->asset = $asset;
        $this->amount = $amount;
        $this->spend_id = $spend_id;
    }
}

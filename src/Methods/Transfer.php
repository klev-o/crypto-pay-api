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
     * Telegram user ID. User must have previously used @CryptoBot (@CryptoTestnetBot for testnet).
     * @var int
     */
    public int $user_id;
    /**
     * Currency code. Supported assets: “BTC”, “TON”, “ETH” (testnet only), “USDT”, “USDC” and “BUSD”.
     * @var string
     */
    public string $asset;
    /**
     * Amount of the transfer in float. For example: 125.50
     * @var string
     */
    public string $amount;
    /**
     * Unique ID to make your request idempotent and ensure that only one of the transfers with the same spend_id will
     * be accepted by Crypto Pay API. This parameter is useful when the transfer should be retried (i.e. request
     * timeout, connection reset, 500 HTTP status, etc). It can be some unique withdrawal identifier for example.
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
     * Optional. Pass true if the user should not receive a notification about the transfer. Default is false.
     * @var bool|null
     */
    public bool $disable_send_notification = false;

    public function __construct(int $user_id, string $asset, string $amount, string $spend_id)
    {
        $this->user_id = $user_id;
        $this->asset = $asset;
        $this->amount = $amount;
        $this->spend_id = $spend_id;
    }
}
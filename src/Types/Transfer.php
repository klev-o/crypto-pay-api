<?php

namespace Klev\CryptoPayApi\Types;

/**
 * class Transfer
 *
 * @link https://help.crypt.bot/crypto-pay-api#Transfer
 */
class Transfer extends BaseType
{
    /**
     * Unique ID for this transfer.
     * @var int
     */
    public int $transfer_id;
    /**
     * Telegram user ID the transfer was sent to.
     * @var string
     */
    public string $user_id;
     /**
     * Cryptocurrency alphabetic code. 
     * Currently, can be “USDT”, “TON”, “BTC”, “ETH”, “LTC”, “BNB”, “TRX” and “USDC” (and “JET” for testnet).
     * @var string
     */
    public string $asset;
    /**
     * Amount of the transfer in float.
     * @var string
     */
    public string $amount;
    /**
     * Status of the transfer, can only be "completed".
     * @var string
     */
    public string $status;
    /**
     * Date the transfer was completed in ISO 8601 format.
     * @var string
     */
    public string $completed_at;
    /**
     * Optional. Comment for this transfer.
     * @var string|null
     */
    public ?string $comment = null;
}
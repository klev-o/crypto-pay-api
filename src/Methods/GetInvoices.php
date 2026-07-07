<?php

namespace Klev\CryptoPayApi\Methods;

/**
 * class GetInvoices
 *
 * @link https://help.crypt.bot/crypto-pay-api#getInvoices
 */
class GetInvoices extends BaseMethod
{
    /**
     * Optional. Currency codes separated by comma. Supported assets: “BTC”, “TON”, “ETH” (testnet only), “USDT”,
     * “USDC” and “BUSD”. Defaults to all assets.
     * @var string|null
     */
    public ?string $asset = null;
    /**
     * Optional. Invoice IDs separated by comma.
     * @var string|null
     */
    public ?string $invoice_ids = null;
    /**
     * Optional. Status of invoices to be returned. Available statuses: “active” and “paid”. Defaults to all statuses.
     * @var string|null
     */
    public ?string $status = null;
    /**
     * Optional. Offset needed to return a specific subset of invoices. Default is 0.
     * @var int|null
     */
    public ?int $offset = null;
    /**
     * Optional. Number of invoices to be returned. Values between 1-1000 are accepted. Defaults to 100.
     * @var int|null
     */
    public ?int $count = null;
}
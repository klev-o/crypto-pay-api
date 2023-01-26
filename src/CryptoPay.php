<?php

namespace Klev\CryptoPayApi;

use GuzzleHttp\Client;
use Klev\CryptoPayApi\Methods\CreateInvoice;
use Klev\CryptoPayApi\Methods\GetInvoices;
use Klev\CryptoPayApi\Methods\Transfer as MethodTransfer;
use Klev\CryptoPayApi\Types\Transfer as TypeTransfer;
use Klev\CryptoPayApi\Types\Invoice;
use Klev\CryptoPayApi\Types\Update;
use Psr\Http\Client\ClientInterface;

/**
 * class CryptoPay
 *
 * Crypto Pay is a payment system based on @CryptoBot that allows you to accept  payments in crypto and transfer coins
 * to users using our API.
 *
 * @link https://help.crypt.bot/crypto-pay-api
 */
class CryptoPay
{
    /**
     * Api token of your application created via @CryptoBot (@CryptoTestnetBot for testnet). Send the command /pay
     * to create a new app and get API Token
     * @var string
     */
    private string $token;
    /**
     * Mainnet endpoint
     * @var string
     */
    private string $apiEndpoint = 'https://pay.crypt.bot/api';
    /**
     * Testnet endpoint
     * @var string
     */
    private string $apiEndpointTest = 'https://testnet-pay.crypt.bot/api';
    /**
     * Whether to use the app on the testnet
     * @var bool|null
     */
    private bool $isTestnet;
    /**
     * Webhook request body (unparsed JSON string)
     * @var string|null
     */
    private ?string $webhookUpdatesRaw = null;
    /**
     * HTTP Client
     * @var ClientInterface|Client
     */
    private ClientInterface $apiClient;
    /**
     * Array of listeners for webhook updates
     * @var array
     */
    private array $listeners = [];
    /**
     * Enable events, disabled by default
     * @var bool
     */
    private bool $enableEvents = false;

    public function __construct(string $token, ?bool $isTestnet = false, ?CryptoPayHttpClientInterface $client = null)
    {
        $this->token = $token;
        $this->isTestnet = $isTestnet;
        $this->apiClient = $client ?? new Client();
    }

    /**
     * Use this method to test your app's authentication token. Requires no parameters. On success, returns basic
     * information about an app.
     *
     * @link https://help.crypt.bot/crypto-pay-api#getMe
     *
     * @return array
     * @throws CryptoPayException
     */
    public function getMe(): array
    {
        $out = $this->request('getMe');
        return $out['result'];
    }

    /**
     * Use this method to create a new invoice. On success, returns an object of the created invoice.
     *
     * @link https://help.crypt.bot/crypto-pay-api#createInvoice
     *
     * @param CreateInvoice $createInvoice
     * @return Invoice
     * @throws CryptoPayException
     */
    public function createInvoice(CreateInvoice $createInvoice): Invoice
    {
        $out = $this->request('createInvoice', ['query' => $createInvoice->toArray()]);
        return new Invoice($out['result']);
    }

    /**
     * Use this method to send coins from your app's balance to a user. On success, returns object of completed
     * transfer. First, you need to enable this method in the security settings of your app.
     * Open @CryptoBot (@CryptoTestnetBot for testnet), go to Crypto Pay → My Apps, choose an app, then go to
     * Security -> Transfers... and tap Enable.
     *
     * @link https://help.crypt.bot/crypto-pay-api#transfer
     *
     * @param MethodTransfer $transfer
     * @return TypeTransfer
     * @throws CryptoPayException
     */
    public function transfer(MethodTransfer $transfer): TypeTransfer
    {
        $out = $this->request('transfer', ['query' => $transfer->toArray()]);
        return new TypeTransfer($out['result']);
    }

    /**
     * Use this method to get invoices of your app. On success, returns array of invoices.
     *
     * @link https://help.crypt.bot/crypto-pay-api#getInvoices
     *
     * @return Invoice[]
     * @throws CryptoPayException
     */
    public function getInvoices(?GetInvoices $getInvoices = null): array
    {
        $params = $getInvoices ? $getInvoices->toArray() : [];
        $out = $this->request('getInvoices', ['query' => $params]);

        if (!isset($out['result']['items'])) {
            return [];
        }

        return array_map(static function($item) {
            return new Invoice($item);
        }, $out['result']['items']);
    }

    /**
     * Use this method to get a balance of your app. Returns array of assets.
     *
     * @link https://help.crypt.bot/crypto-pay-api#getBalance
     *
     * @return array
     * @throws CryptoPayException
     */
    public function getBalance(): array
    {
        $out = $this->request('getBalance');
        return $out['result'];
    }

    /**
     * Use this method to get exchange rates of supported currencies. Returns array of currencies.
     *
     * @link https://help.crypt.bot/crypto-pay-api#getExchangeRates
     *
     * @return array
     * @throws CryptoPayException
     */
    public function getExchangeRates(): array
    {
        $out = $this->request('getExchangeRates');
        return $out['result'];
    }

    /**
     * Use this method to get a list of supported currencies. Returns array of currencies.
     *
     * @link https://help.crypt.bot/crypto-pay-api#getCurrencies
     *
     * @return array
     * @throws CryptoPayException
     */
    public function getCurrencies(): array
    {
        $out = $this->request('getCurrencies');
        return $out['result'];
    }

    /**
     * Attach an event handler
     *
     * @param string $paidType
     * @param callable $callback
     * @return void
     */
    public function on(string $paidType, callable $callback)
    {
        $this->listeners[$paidType][] = $callback;
    }

    /**
     * Getting webhook updates
     *
     * @link https://help.crypt.bot/crypto-pay-api#webhook-updates
     *
     * @param bool $throwVerifyError - Throw an exception if the webhook fails verification. The default is set to true.
     * If set to false, the exception will not be thrown, it will simply return false
     * @return void
     * @throws CryptoPayException
     */
    public function getWebhookUpdates(bool $throwVerifyError = true):? Update
    {
        $updates = null;
        if ($this->getWebhookUpdatesRaw() && $this->verifyWebhookUpdates($throwVerifyError)) {
            $data = json_decode($this->getWebhookUpdatesRaw(), true);
            $updates = $data ? new Update($data) : null;
            if ($updates && $this->isEnableEvents()) {
                $this->trigger($updates);
            }
        }
        return $updates;
    }


    /**
     * Getting request body (unparsed JSON string)
     * @return string|null
     */
    public function getWebhookUpdatesRaw(): ?string
    {
        if (!$this->webhookUpdatesRaw) {
            $this->webhookUpdatesRaw = file_get_contents('php://input') ?: null;

        }
        return $this->webhookUpdatesRaw;
    }

    /**
     * @return bool
     */
    public function isEnableEvents(): bool
    {
        return $this->enableEvents;
    }

    /**
     * @param bool $enableEvents
     */
    public function setEnableEvents(bool $enableEvents): void
    {
        $this->enableEvents = $enableEvents;
    }

    /**
     * Getting correct api uri
     * @param $method
     * @return string
     */
    private function getApiUri($method): string
    {
        $endpoint = $this->isTestnet ? $this->apiEndpointTest : $this->apiEndpoint;
        return $endpoint . '/' . $method;
    }

    /**
     * Triggering listeners by update_type
     * @param Update $update
     * @return void
     */
    private function trigger(Update $update)
    {
        if (isset($this->listeners[$update->update_type])) {
            foreach ($this->listeners[$update->update_type] as $listener) {
                $listener($update);
            }
        }
    }

    /**
     * @param string $method
     * @param array $data
     * @return array
     * @throws CryptoPayException
     */
    private function request(string $method, array $data = []): array
    {
        try {
            $uri = $this->getApiUri($method);
            $params = array_merge_recursive($data, [
               'headers' => [
                   'Crypto-Pay-API-Token' => $this->token
               ]
            ]);

            $response = $this->apiClient->get($uri, $params);

            $body = (string)$response->getBody();
            $out = json_decode($body,true, 512, JSON_THROW_ON_ERROR);

            if (isset($out['ok']) && $out['ok'] === true && isset($out['result'])) {
                return $out;
            }

            throw new CryptoPayException('Unexpected response: ' . $body);
        } catch (\Exception $e) {
            throw new CryptoPayException($e->getMessage());
        }
    }

    /**
     * Webhook verification
     *
     * @link https://help.crypt.bot/crypto-pay-api#verifying-webhook-updates
     *
     * @param bool $throwVerifyError - Throw an exception if the webhook fails verification. The default is set to true.
     * If set to false, the exception will not be thrown, it will simply return false
     * @return bool
     * @throws CryptoPayException
     */
    private function verifyWebhookUpdates(bool $throwVerifyError = true): bool
    {
        try {
            $error = 'Webhook data not verified';
            $signature = $_SERVER['HTTP_CRYPTO_PAY_API_SIGNATURE'] ?? false;

            if (!$signature) {
                throw new CryptoPayException($error . ': HTTP_CRYPTO_PAY_API_SIGNATURE parameter missing');
            }

            $secret = hash('sha256', $this->token, true);
            $calcHash = hash_hmac('sha256', $this->getWebhookUpdatesRaw(), $secret);

            if ($signature !== $calcHash) {
                throw new CryptoPayException($error . ': invalid hash');
            }

            return true;
        } catch (\Exception $e) {
            if (!$throwVerifyError) {
                return false;
            }
            throw $e;
        }
    }
}
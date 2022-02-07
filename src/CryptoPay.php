<?php

namespace Klev\CryptoPayApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Klev\CryptoPayApi\Methods\CreateInvoice;
use Klev\CryptoPayApi\Methods\GetInvoices;
use Klev\CryptoPayApi\Methods\Transfer as MethodTransfer;
use Klev\CryptoPayApi\Types\Transfer as TypeTransfer;
use Klev\CryptoPayApi\Types\Invoice;
use Klev\CryptoPayApi\Types\Update;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;

/**
 * class CryptoPay
 *
 * Crypto Pay is a payment system based on @CryptoBot that allows you to accept  payments in crypto and transfer coins
 * to users using our API.
 *
 * @see https://telegra.ph/Crypto-Pay-API-11-25
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
     * List listeners for webhook events
     * @var array
     */
    private array $listeners = [];
    /**
     * DI
     * @var ContainerInterface|null
     */
    private ?ContainerInterface $container = null;

    public function __construct(string $token, ?bool $isTestnet = false)
    {
        $this->token = $token;
        $this->isTestnet = $isTestnet;
        $this->apiClient = new Client();
    }

    /**
     * Use this method to test your app's authentication token. Requires no parameters. On success, returns basic
     * information about an app.
     *
     * @see https://telegra.ph/Crypto-Pay-API-11-25#getMe
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
     * @see https://telegra.ph/Crypto-Pay-API-11-25#createInvoice
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
     * Use this method to send coins from your app's balance to a user. On success, returns object of
     * completed transfer.
     *
     *
     * @see https://telegra.ph/Crypto-Pay-API-11-25#transfer
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
     * @see https://telegra.ph/Crypto-Pay-API-11-25#getInvoices
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
     * @see https://telegra.ph/Crypto-Pay-API-11-25#getBalance
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
     * @see https://telegra.ph/Crypto-Pay-API-11-25#getExchangeRates
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
     * @see https://telegra.ph/Crypto-Pay-API-11-25#getCurrencies
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
     * @param string $paidType
     * @param mixed $callback
     * @return void
     * @throws CryptoPayException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function on(string $paidType, $callback)
    {
        if ($callback instanceof \Closure) {
            $this->listeners[$paidType][] = $callback;
        } else {
            if (!$this->container) {
                throw new CryptoPayException('For use in events of something other than anonymous functions, 
                set the container using the setContainer method');
            }
            $this->listeners[$paidType][] = $this->container->get($callback);
        }
    }

    /**
     * Getting webhook updates
     *
     * @see https://telegra.ph/Crypto-Pay-API-11-25#Webhooks
     *
     * @param bool $throwVerifyError - Throw an exception if the webhook fails verification. The default is set to true.
     * If set to false, the exception will not be thrown, it will simply return false
     * @return void
     * @throws CryptoPayException
     */
    public function getWebhookUpdates(bool $throwVerifyError = true): void
    {
        if ($this->getWebhookUpdatesRaw() && $this->verifyWebhookUpdates($throwVerifyError)) {
            $data = json_decode($this->getWebhookUpdatesRaw(), true);
            $updates = $data ? new Update($data) : null;
            if ($updates) {
                $this->trigger($updates->update_type, $updates);
            }
        }
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
     * Set Container
     * @param ContainerInterface|null $container
     * @return void
     */
    public function setContainer(?ContainerInterface $container): void
    {
        $this->container = $container;
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
     * Triggering listeners by name
     * @param $name
     * @param Update $update
     * @return void
     */
    private function trigger($name, Update $update)
    {
        if (isset($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $listener) {
                $listener($update, $this->container);
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
            //var_dump($uri . http_build_query($params['query']));

            $response = $this->apiClient->get($uri, $params);

            $body = (string)$response->getBody();
            $out = json_decode($body,true, 512, JSON_THROW_ON_ERROR);

            if (isset($out['ok']) && $out['ok'] === true && isset($out['result'])) {
                return $out;
            }
            throw new \Exception('Unexpected response: ' . $body);
        } catch (GuzzleException $e) {
            throw new CryptoPayException('GuzzleException: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new CryptoPayException($e->getMessage());
        }
    }

    /**
     * Webhook verification
     *
     * @see https://telegra.ph/Crypto-Pay-API-11-25#Verifying-webhook-updates
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
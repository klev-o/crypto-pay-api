<?php

use Klev\CryptoPayApi\CryptoPay;
use Klev\CryptoPayApi\Enums\PaidType;
use Klev\CryptoPayApi\Types\Update;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require_once '../vendor/autoload.php';

$api = new CryptoPay('your token');
$api->setEnableEvents(true);

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
    LoggerInterface::class => function(\DI\Container $c) {
        $log = new Logger('App');
        $log->pushHandler(new StreamHandler('../var/logs/app.log'));
        return $log;
    },
    InvoicePaidListener::class => function(\DI\Container $c) {
        return new InvoicePaidListener($c->get(LoggerInterface::class));
    }
]);
$container = $builder->build();

try {
    $api->on(PaidType::INVOICE_PAID, $container->get(InvoicePaidListener::class));
} catch (Throwable $e) {
    $container->get(LoggerInterface::class )->error('webhook updates error', [$e->getMessage()]);
}


class InvoicePaidListener
{
    private Logger $log;

    public function __construct(Logger $log)
    {
        $this->log = $log;
    }
    public function __invoke(Update $update)
    {
        //Someone paid the bill, do something with the data
        $this->log->info('InvoicePaid', (array)$update);
    }
}
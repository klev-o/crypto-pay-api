<?php

use Klev\CryptoPayApi\CryptoPay;
use Klev\CryptoPayApi\Enums\Status;
use Klev\CryptoPayApi\Methods\CreateInvoice;
use Klev\CryptoPayApi\Methods\GetInvoices;
use Klev\CryptoPayApi\Methods\Transfer;

require_once '../vendor/autoload.php';

$api = new CryptoPay('your token');


//getMe-----------------------------------------------------------------------------------------------------------------
/**@var array $result*/
$result = $api->getMe();


//create Invoice--------------------------------------------------------------------------------------------------------
$invoice = new CreateInvoice('TON', '0.05');
$invoice->allow_anonymous = false;
$invoice->allow_comments = false;
$invoice->paid_btn_name = 'openChannel';
$invoice->paid_btn_url = 'https://t.me/your-channel-link';
$invoice->description = 'Pay and go)';
$invoice->hidden_message = 'Any secret text';

/**@var \Klev\CryptoPayApi\Types\Invoice $result*/
$result = $api->createInvoice($invoice);


//transfer--------------------------------------------------------------------------------------------------------------
$telegramUserId = 11111111111;
$anyUniqueId = 'jr3jgkldp[w3';

$transfer = new Transfer($telegramUserId, 'TON', '0.0777', $anyUniqueId);
$transfer->comment = 'Relax';

/**@var \Klev\CryptoPayApi\Types\Transfer $result*/
$result = $api->transfer($transfer);


//getInvoices-----------------------------------------------------------------------------------------------------------
/**@var \Klev\CryptoPayApi\Types\Invoice[] $invoices*/
$invoices = $api->getInvoices();

//You can also use filtering by currencies, ids, status, adjust the offset and the amount of data returned

$getInvoices = new GetInvoices();
$getInvoices->status = Status::ACTIVE;
$getInvoices->asset = 'TON';
$invoices = $api->getInvoices($getInvoices);


//getBalance------------------------------------------------------------------------------------------------------------
/**@var array $result*/
$result = $api->getBalance();


//getExchangeRates------------------------------------------------------------------------------------------------------
/**@var array $result*/
$result = $api->getExchangeRates();


//getExchangeRates------------------------------------------------------------------------------------------------------
/**@var array $result*/
$result = $api->getCurrencies();
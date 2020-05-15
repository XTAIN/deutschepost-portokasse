## Client for Deutsche Post Portokasse

## Installation

The package is available via Composer. To install the latest version from Packagist, run:

```
composer require xtain/deutschepost-portokasse
```

## Example

```php
<?php

require_once './vendor/autoload.php';

// Authenticate with same credentials as you login to https://portokasse.deutschepost.de/portokasse/
$portokasse = new \XTAIN\DeutschePostPortokasse\Client(
    'mail@example.com',
    '******'
);

echo '####################################'.PHP_EOL;
echo '  => Current balance: ' . number_format($portokasse->getBalance() / 100, 2, ',', '.'). ' €'.PHP_EOL;
echo '####################################'.PHP_EOL;
echo PHP_EOL;
echo '##### Transactions #####'.PHP_EOL;

foreach ($portokasse->getJournal() as $journal) {
    /** @var \XTAIN\DeutschePostPortokasse\Model\JournalEntry $entry */
    foreach ($journal as $entry) {
        echo ' Date:            ' . $entry->getDate()->format(\DateTime::W3C) . PHP_EOL;
        echo ' Amount:          ' .  number_format($entry->getAmount() / 100, 2, ',', '.'). ' €'.PHP_EOL;
        echo ' Accounting Text: ' . $entry->getAccountingText() . PHP_EOL;
        echo ' Channel:         ' . $entry->getChannel() . PHP_EOL;
        echo ' Shop Order ID:   ' . $entry->getShopOrderId() . PHP_EOL;
        echo ' State:           ' . $entry->getState() . PHP_EOL;
        echo ' Type:            ' . $entry->getType() . PHP_EOL;
        echo '=============================='.PHP_EOL;
    }
}

/*
try {
    // load 1€ cent by SEPA direct debit
    $portokasse->loadMoneyDirectDebit(1);
} catch (\XTAIN\DeutschePostPortokasse\Exception\InvalidPaymentAmountException $e) {
    // increase amount
}
*/
```

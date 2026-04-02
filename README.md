
# Laravel E-Invoice Suite

Multi-provider E-Invoice integration for Laravel 8+ (EY, Adaequare, etc.)

## Features
- Plug-and-play support for multiple e-invoice providers
- Easily extendable for new providers
- Simple configuration and usage

---

## Installation

1. **Add the package to your Laravel project's `composer.json` repositories:**

   ```json
   "repositories": [
     {
       "type": "path",
       "url": "packages/laravel-einvoice-suite"
     }
   ]
   ```

2. **Require the package via Composer:**

   ```sh
   composer require laravel-einvoice-suite
   ```

3. **Publish the configuration file:**

   ```sh
   php artisan vendor:publish --tag=einvoice-config
   ```

4. **Set your credentials** in `.env` or directly in `config/einvoice.php` for each provider.

---

## Usage Example

```php
use LaravelEInvoiceSuite\EInvoiceManager;

$params = [
    'suppGstin' => '29ABCDE1234F2Z5',
    'docNo' => 'INV-1001',
    'docDate' => '01/04/2026',
    'docType' => 'INV',
    'custGstin' => '27ABCDE1234F1Z6',
    'custOrSupName' => 'Customer Name',
    'custOrSupAddr1' => 'Customer Address',
    'custPincode' => '400001',
    'billToState' => '27',
    'invAssessableAmt' => 1000,
    'invIgstAmt' => 0,
    'invCgstAmt' => 90,
    'invSgstAmt' => 90,
    'totalInvoiceAmount' => 1180,
    'lineItems' => [
        [
            'SlNo' => 1,
            'PrdDesc' => 'Product 1',
            'IsServc' => 'N',
            'HsnCd' => '1001',
            'Qty' => 1,
            'Unit' => 'NOS',
            'UnitPrice' => 1000,
            'TotAmt' => 1000,
            'Discount' => 0,
            'AssAmt' => 1000,
            'GstRt' => 18,
            'CgstAmt' => 90,
            'SgstAmt' => 90,
            'IgstAmt' => 0,
            'TotItemVal' => 1180
        ]
    ]
];

// EY Provider Example
$manager = new EInvoiceManager('ey');
$manager->setParameters($params);
$response = $manager->generateIrn();

// Adaequare Provider Example
$manager = new EInvoiceManager('adaequare');
$manager->setParameters($params);
$response = $manager->generateIrn();

// You can also call other methods:
// $manager->generateEwayBill();
// $manager->cancelEwayBill();
// $manager->cancelIrn();

// Handle the response
if ($response['status']) {
    // Success
    print_r($response['data']);
} else {
    // Error
    echo 'Error: ' . $response['data'];
}
```

---

## Configuration

See `config/einvoice.php` for provider credentials and settings. You can add more providers as needed.

---

## Extending

To add a new provider, create a new class in `src/Providers/` implementing the `EInvoiceProviderInterface` and add it to the manager and config.

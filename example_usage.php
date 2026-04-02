<?php

use LaravelEInvoiceSuite\EInvoiceManager;

// Example: Set parameters for invoice
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

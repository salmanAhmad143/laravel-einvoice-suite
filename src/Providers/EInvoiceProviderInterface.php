<?php

namespace LaravelEInvoiceSuite\Providers;

interface EInvoiceProviderInterface
{
    public function authenticate();
    public function generateIrn();
    public function generateEwayBill();
    public function cancelEwayBill();
    public function cancelIrn();
    public function setParameters(array $params): void;
}

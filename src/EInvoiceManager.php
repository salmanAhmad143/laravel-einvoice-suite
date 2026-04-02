<?php

namespace LaravelEInvoiceSuite;

use LaravelEInvoiceSuite\Providers\EInvoiceProviderInterface;
use LaravelEInvoiceSuite\Providers\EYProvider;
use LaravelEInvoiceSuite\Providers\AdaequareProvider;

class EInvoiceManager
{
    protected $provider;

    public function __construct($providerName = null, array $params = [])
    {
        $providerName = $providerName ?: config('einvoice.default_provider');
        $this->provider = $this->resolveProvider($providerName);
        if ($params) {
            $this->provider->setParameters($params);
        }
    }

    protected function resolveProvider($providerName)
    {
        switch (strtolower($providerName)) {
            case 'ey':
                return new EYProvider();
            case 'adaequare':
                return new AdaequareProvider();
            default:
                throw new \Exception("Unknown E-Invoice provider: $providerName");
        }
    }

    public function setProvider($providerName)
    {
        $this->provider = $this->resolveProvider($providerName);
    }

    public function setParameters(array $params)
    {
        $this->provider->setParameters($params);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->provider, $method], $args);
    }
}

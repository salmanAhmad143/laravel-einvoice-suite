<?php

namespace LaravelEInvoiceSuite;

use Illuminate\Support\ServiceProvider;

class EInvoiceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/einvoice.php' => config_path('einvoice.php'),
        ], 'einvoice-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/einvoice.php', 'einvoice');
    }
}

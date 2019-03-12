<?php

namespace PhpCoda\LaravelCoda\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelCodaFacades extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'coda-payment-gateway-api';
    }
}

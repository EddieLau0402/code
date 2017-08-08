<?php

namespace Eddie\CouponCode\Facades;


use Illuminate\Support\Facades\Facade;

class CouponCode extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Eddie\CouponCode\CouponCodeManager';
    }
}
<?php

namespace Eddie\CouponCode;


use Eddie\CouponCode\Providers\Yuanhui;

class CouponCodeManager
{
    public function provider($provider)
    {
        switch (strtolower($provider)) {
            case 'yuanhui':
                $config = config('coupon_code.yuanhui');
                return new Yuanhui($config);


            default:
                throw new \Exception('找不到相应的provider', 500);
        }
    }
}
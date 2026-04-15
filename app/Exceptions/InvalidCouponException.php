<?php

namespace App\Exceptions;

use Exception;

class InvalidCouponException extends Exception
{
    protected $message = 'This coupon is invalid or has expired.';
}

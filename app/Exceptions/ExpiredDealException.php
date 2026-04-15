<?php

namespace App\Exceptions;

use Exception;

class ExpiredDealException extends Exception
{
    protected $message = 'This deal is no longer active.';
}

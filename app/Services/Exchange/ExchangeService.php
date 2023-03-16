<?php

namespace App\Services\Exchange;

class ExchangeService
{
    public function convert($base, $target, $amount)
    {
        return $amount * 2;
    }
}

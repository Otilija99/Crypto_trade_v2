<?php

namespace App\Client;

interface ApiClientInterface
{
    public function getTopCryptocurrencies(): array;
    public function getCurrentPrice(string $symbol): float;
}



<?php

namespace App\Model;

class Cryptocurrency
{
    private string $symbol;
    private string $name;
    private float $price;

    public function __construct(string $symbol, string $name, float $price)
    {
        $this->symbol = $symbol;
        $this->name = $name;
        $this->price = $price;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}



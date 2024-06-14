<?php

namespace App\Service;

use App\Database\Connection;
use App\Client\ApiClientInterface;
use App\Model\User;
use Carbon\Carbon;

class BuyService
{
    public static function buyCrypto(User $user, string $symbol, float $amount, ApiClientInterface $apiClient): string
    {
        $pdo = Connection::getPDO();
        $price = $apiClient->getCurrentPrice($symbol);
        $quantity = $amount / $price;

        // Insert transaction into the database
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, symbol, quantity, price, type, created_at) VALUES (?, ?, ?, ?, 'buy', ?)");
        $stmt->execute([
            $user->getId(),
            $symbol,
            $quantity,
            $price,
            Carbon::now()->toDateTimeString()
        ]);

        return "Purchase successful: Bought {$quantity} {$symbol} for \${$amount}";
    }
}


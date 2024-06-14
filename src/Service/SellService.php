<?php

namespace App\Service;

use App\Database\Connection;
use App\Client\ApiClientInterface;
use App\Model\User;
use Carbon\Carbon;

class SellService
{
    public static function sellCrypto(User $user, string $symbol, float $quantity, ApiClientInterface $apiClient): string
    {
        $pdo = Connection::getPDO();
        $price = $apiClient->getCurrentPrice($symbol);
        $amount = $quantity * $price;

        // Check if user has enough quantity
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_quantity FROM transactions WHERE user_id = ? AND symbol = ? AND type = 'buy'");
        $stmt->execute([$user->getId(), $symbol]);
        $totalBought = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_quantity FROM transactions WHERE user_id = ? AND symbol = ? AND type = 'sell'");
        $stmt->execute([$user->getId(), $symbol]);
        $totalSold = $stmt->fetchColumn();

        $availableQuantity = $totalBought - $totalSold;

        if ($quantity > $availableQuantity) {
            return "Insufficient quantity to sell";
        }

        // Insert transaction into the database
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, symbol, quantity, price, type, created_at) VALUES (?, ?, ?, ?, 'sell', ?)");
        $stmt->execute([
            $user->getId(),
            $symbol,
            $quantity,
            $price,
            Carbon::now()->toDateTimeString()
        ]);

        return "Sell successful: Sold {$quantity} {$symbol} for \${$amount}";
    }
}


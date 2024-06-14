<?php

namespace App\Service;

use App\Database\Connection;
use App\Client\ApiClientInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Model\User;

class WalletService
{
    public static function getWallet(ConsoleOutput $output, User $user, ApiClientInterface $apiClient): void
    {
        $pdo = Connection::getPDO();
        $stmt = $pdo->prepare("SELECT symbol, SUM(CASE WHEN type = 'buy' THEN quantity ELSE -quantity END) as 
    total_quantity FROM transactions WHERE user_id = ? GROUP BY symbol");
        $stmt->execute([$user->getId()]);
        $wallet = $stmt->fetchAll();

        $table = new Table($output);
        $table->setHeaders(['Symbol', 'Quantity', 'Current Price', 'Value']);

        $totalValue = 0;

        foreach ($wallet as $entry) {
            $symbol = $entry['symbol'];
            $quantity = $entry['total_quantity'];
            $currentPrice = $apiClient->getCurrentPrice($symbol);
            $value = $quantity * $currentPrice;
            $totalValue += $value;

            $table->addRow([
                $symbol,
                $quantity,
                '$' . $currentPrice,
                '$' . $value
            ]);
        }

        $table->addRow(new TableSeparator());
        $table->addRow(['Total Value', '', '', '$' . $totalValue]);

        $table->render();
    }
}


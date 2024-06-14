<?php

namespace App\Service;

use App\Database\Connection;
use App\Model\User;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class TransactionService
{
    public static function getTransactions(ConsoleOutput $output, User $user): void
    {
        $pdo = Connection::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ?");
        $stmt->execute([$user->getId()]);
        $transactions = $stmt->fetchAll();

        $table = new Table($output);
        $table->setHeaders(['ID', 'Symbol', 'Quantity', 'Price', 'Type', 'Created At']);

        foreach ($transactions as $transaction) {
            $table->addRow([
                $transaction['id'],
                $transaction['symbol'],
                $transaction['quantity'],
                '$' . $transaction['price'],
                $transaction['type'],
                $transaction['created_at']
            ]);
        }

        $table->render();
    }
}


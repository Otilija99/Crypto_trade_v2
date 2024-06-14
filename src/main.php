<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Client\CoinMarketCapApiClient;
use App\Service\BuyService;
use App\Service\SellService;
use App\Service\TransactionService;
use App\Service\WalletService;
use App\Model\User;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

$output = new ConsoleOutput();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$apiKey = getenv('COINMARKETCAP_API_KEY');
$apiClient = new CoinMarketCapApiClient($apiKey);

$user = new User(1, 'Demo User');

$output->writeln("Welcome to the Crypto Trading App!");

while (true) {
    $output->writeln([
        '',
        '1. List Top Cryptocurrencies',
        '2. Buy Cryptocurrency',
        '3. Sell Cryptocurrency',
        '4. View Wallet',
        '5. View Transactions',
        '6. Exit',
        ''
    ]);

    $option = readline("Choose an option: ");

    switch ($option) {
        case 1:
            $cryptocurrencies = $apiClient->getTopCryptocurrencies();
            $table = new Table($output);
            $table->setHeaders(['Symbol', 'Name', 'Price (USD)']);

            foreach ($cryptocurrencies as $crypto) {
                $table->addRow([$crypto->getSymbol(), $crypto->getName(), '$' . $crypto->getPrice()]);
            }

            $table->render();
            break;
        case 2:
            $symbol = readline("Enter cryptocurrency symbol to buy: ");
            $amount = (float)readline("Enter amount to buy in USD: ");
            $message = BuyService::buyCrypto($user, $symbol, $amount, $apiClient);
            $output->writeln($message);
            break;
        case 3:
            $symbol = readline("Enter cryptocurrency symbol to sell: ");
            $quantity = (float)readline("Enter quantity to sell: ");
            $message = SellService::sellCrypto($user, $symbol, $quantity, $apiClient);
            $output->writeln($message);
            break;
        case 4:
            WalletService::getWallet($output, $user, $apiClient);
            break;
        case 5:
            TransactionService::getTransactions($output, $user);
            break;
        case 6:
            exit("Goodbye!");
        default:
            $output->writeln("Invalid option. Please try again.");
    }
}

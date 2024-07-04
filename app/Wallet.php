<?php


namespace CryptoTrade\App;

use CryptoTrade\App\Api\CoinMC;
use CryptoTrade\App\Api\CryptoApi;
use CryptoTrade\App\Database\Sqlite;
use CryptoTrade\App\Models\Transaction;

class Wallet
{
    private CryptoApi $cryptoApi;

    private float $money;
    private Sqlite $database;
    private array $transactions;
    private array $owned;


    public function __construct()
    {
        $this->cryptoApi = new CoinMC();
        $this->database = new Sqlite();
        $this->transactions = $this->database->loadTransactions();
        $this->owned = $this->database->loadOwned();
        $this->money = $this->database->loadMoney();
    }

    public function owned(string $name, float $amount): void
    {
        if (isset($this->owned[$name])) {
            $this->owned[$name] += $amount;
        } else {
            $this->owned[$name] = $amount;
        }
    }


    public function buy(): void
    {
        $userCrypto = ucfirst($_POST['crypto_name']);
        $userAmount = $_POST['crypto_amount'];
        $cryptoList = $this->cryptoApi->getResponse();
        foreach ($cryptoList as $item) {
            if ($userCrypto === $item->getName()) {
                $price = $item->getPrice();
                $name = $item->getName();
                $totalCrypto = $userAmount / $price;
                $this->money -= $userAmount;
                $this->owned($name, $totalCrypto);
                $this->transactions[] = new Transaction("Purchased", $name, $userAmount, $totalCrypto, $price);
                $this->database->insert($this->money, $this->owned, $this->transactions);
                break;
            }
        }
    }

    public function sell(): void
    {
        $userSell = ucfirst($_POST['name']);
        if (!empty($userSell)) {
            $cryptoList = $this->cryptoApi->getResponse();
            if (isset($this->owned[$userSell])) {
                foreach ($cryptoList as $crypto) {
                    if ($userSell === $crypto->getName()) {
                        $price = $crypto->getPrice();
                        $totalDollars = $price * $this->owned[$userSell];
                        $this->money += $totalDollars;
                        $this->transactions[] = new Transaction("Sold", $userSell, $this->owned[$userSell], $totalDollars, $price);
                        unset($this->owned[$userSell]);
                        $this->database->insert($this->money, $this->owned, $this->transactions);
                        break;
                    }
                }
            }
        }
    }


    public function getTransaction(): array
    {
        return $this->transactions;
    }

    public function getOwned(): array
    {
        return $this->owned;
    }

    public function getMoney(): float
    {
        return $this->money;
    }
}
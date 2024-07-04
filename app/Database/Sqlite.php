<?php


namespace CryptoTrade\App\Database;

use CryptoTrade\App\Models\Transaction;
use Medoo\Medoo;

class Sqlite
{
    protected Medoo $db;


    public function __construct()
    {
        $this->db = new Medoo([
            'database_type' => 'sqlite',
            'database_name' => 'storage/database.sqlite',
        ]);

        $this->create();
    }

    private function create(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS wallet (
            money INTEGER,
            owned TEXT
        )");
        $this->db->exec("CREATE TABLE IF NOT EXISTS transactions (
            trade TEXT,
            crypto_name TEXT,
            spent REAL,
            received REAL,
            price TEXT
        )");
    }

    public function insert(float $money, array $owned, array $transactions): void
    {
        $owned = json_encode($owned);
        $this->db->update("wallet", ["money" => $money, "owned" => $owned]);

        foreach ($transactions as $transaction) {
            if ($transaction instanceof Transaction) {
                $transaction = $transaction->getTransactions();
            }
            $isTransaction = $this->db->get("transactions", "*", [
                "AND" => [
                    "trade" => $transaction['trade'],
                    "crypto_name" => $transaction['crypto_name'],
                    "spent" => $transaction['spent'],
                    "received" => $transaction['received'],
                    "price" => $transaction['price']
                ]
            ]);

            if (!$isTransaction) {
                $this->db->insert("transactions", $transaction);
            }
        }
    }

    public function loadTransactions(): array
    {
        return $this->db->select("transactions", ["trade", "crypto_name", "spent", "received", "price"]);
    }

    public function loadOwned(): array
    {
        $owned = $this->db->get("wallet", "owned");
        return $owned ? json_decode($owned, true) : [];
    }

    public function loadMoney(): float
    {
        $money = $this->db->get("wallet", "money");
        if ($money === null) {
            $this->db->insert("wallet", ["money" => 1000]);
            return 1000;
        }
        return (float)$money;
    }
}
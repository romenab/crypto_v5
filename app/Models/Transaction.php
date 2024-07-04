<?php


namespace CryptoTrade\App\Models;

class Transaction
{
    private string $trade;
    private string $cryptoName;
    private float $spent;
    private float $received;
    private float $price;


    public function __construct(
        string $trade,
        string $cryptoName,
        float  $spent,
        float  $received,
        float  $price
    )
    {
        $this->trade = $trade;
        $this->cryptoName = $cryptoName;
        $this->spent = $spent;
        $this->received = $received;
        $this->price = $price;
    }

    public function getTransactions(): array
    {
        return [
            'trade' => $this->trade,
            'crypto_name' => $this->cryptoName,
            'spent' => $this->spent,
            'received' => $this->received,
            'price' => $this->price,
        ];
    }


}

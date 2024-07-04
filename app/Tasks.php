<?php


namespace CryptoTrade\App;

use CryptoTrade\App\Api\CoinMC;
use CryptoTrade\App\Api\CryptoApi;

class Tasks
{
    private CryptoApi $cryptoApi;
    private array $latest;

    public function __construct($latest = [])
    {
        $this->cryptoApi = new CoinMC();
        $this->latest = $latest;
    }

    public function latest(): array
    {
        $cryptoList = $this->cryptoApi->getResponse();
        $number = 1;
        foreach ($cryptoList as $item) {
            $this->latest[] = [
                'number' => $number,
                'name' => $item->getName()
            ];
            $number++;
            if ($number > 10) {
                break;
            }
        }
        return $this->latest;
    }

    public function search(): array
    {
        $searchInfo = [];
        if (isset($_GET['symbol'])) {
            $userSymbol = strtoupper($_GET['symbol']);
            $cryptoList = $this->cryptoApi->getResponse();
            foreach ($cryptoList as $item) {
                if ($userSymbol === $item->getSymbol()) {
                    $searchInfo[] = [
                        "name" => $item->getName(),
                        "symbol" => $item->getSymbol(),
                        "price" => number_format($item->getPrice(), 2, '.', ','),
                        "oneHour" => round($item->getOneHour(), 2),
                        "twentyFourHour" => round($item->getTwentyFourHour(), 2),
                        "sevenDays" => round($item->getSevenDays(), 2),
                        "marketCap" => number_format($item->getMarketCap(), 0, '.', ',')
                    ];
                }
            }
        }
        return $searchInfo;
    }
}
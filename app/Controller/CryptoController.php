<?php


namespace CryptoTrade\App\Controller;

use CryptoTrade\App\RedirectResponse;
use CryptoTrade\App\Response;
use CryptoTrade\App\Tasks;
use CryptoTrade\App\Wallet;


class CryptoController
{

    private Tasks $tasks;
    private Wallet $wallet;

    public function __construct()
    {
        $this->tasks = new Tasks();
        $this->wallet = new Wallet();
    }

    public function latest(): Response
    {
        $currencies = $this->tasks->latest();
        return new Response('currencies/latest.twig', ['currencies' => $currencies]);
    }

    public function search(): Response
    {
        $currencies = $this->tasks->search();
        return new Response('currencies/search.twig', ['currencies' => $currencies]);
    }

    public function buyView(): Response
    {
        return new Response('currencies/buy.twig');
    }


    public function buy(): RedirectResponse
    {
        $this->wallet->buy();
        return new RedirectResponse('/transactions');
    }

    public function sellView(): Response
    {
        return new Response('currencies/sell.twig');
    }

    public function sell(): RedirectResponse
    {
        $this->wallet->sell();
        return new RedirectResponse('/transactions');
    }

    public function transactions(): Response
    {
        $currencies = $this->wallet->getTransaction();
        return new Response('currencies/transactions.twig', ['currencies' => $currencies]);
    }
}
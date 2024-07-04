<?php
namespace CryptoTrade\App;
class RedirectResponse
{
    private string $location;
    public function __construct(string $location)
    {
        $this->location = $location;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
}
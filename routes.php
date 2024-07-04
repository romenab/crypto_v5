<?php

use CryptoTrade\App\Controller\CryptoController;

return [
    ['GET', '/latest', [CryptoController::class, 'latest']],
    ['GET', '/search', [CryptoController::class, 'search']],

    ['GET', '/buy', [CryptoController::class, 'buyView']],
    ['POST', '/buy', [CryptoController::class, 'buy']],

    ['GET', '/sell', [CryptoController::class, 'sellView']],
    ['POST', '/sell', [CryptoController::class, 'sell']],

    ['GET', '/transactions', [CryptoController::class, 'transactions']],
];
<?php

require_once "vendor/autoload.php";

$client = new \Entrepot\SDK\Client([
    'clientId' => '8713857137206140'
]);
echo '<pre>', var_dump($client->products->list()), '</pre>';

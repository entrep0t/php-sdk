<?php

require_once "vendor/autoload.php";

$client = new \Entrepot\SDK\Client([
    'clientId' => '8713857137206140'
]);

$client->cart->addItem('5e77a50251a88fabb83f9812');
$client->cart->addItem('5e77a50251a88fabb83f9812');
echo 'Quantity:', $client->cart->get()["content"][0]["quantity"];
$client->cart->pullItem('5e77a50251a88fabb83f9812');
echo 'Quantity:', $client->cart->get()["content"][0]["quantity"];
$client->cart->removeItem('5e77a50251a88fabb83f9812');
echo '<pre>', var_dump($client->cart->get()), '</pre>';

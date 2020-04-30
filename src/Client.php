<?php

namespace Entrepot\SDK;

class Client {
  public $clientId;

  public function __construct($clientId) {
    $this->clientId = $clientId;
  }

  public function log() {
    echo $this->clientId;
  }
}

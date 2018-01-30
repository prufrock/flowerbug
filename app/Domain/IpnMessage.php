<?php namespace App\Domain;

class IpnMessage {

  public $data;

  public function getBuyersEmailAddress() {

    return array_get($this->data, 'payer_email', '');
  }
}
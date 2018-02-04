<?php namespace App\Domain;

class IpnMessage {

  public $data;

  private $responder;

  public function __construct(IpnResponder $responder) {

    $this->responder = $responder;
  }

  public function getBuyersEmailAddress() {

    return array_get($this->data, 'payer_email', '');
  }
  
  public function verifyIpnMessage() {

    return $this->responder->verifyIpnMessage($this->data);
  }
}
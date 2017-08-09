<?php namespace App\Domain;

class PaymentProcessor {

  private $responder;

  public function __construct($responder=NULL) {

    $this->responder = $responder;
  }

  public function process($payment) {

    $this->responder->create(['ipnVars' => $payment]);
  }
}
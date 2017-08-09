<?php namespace App\Domain;

class PaymentProcessor {

  private $responder;

  public function __construct($responder = NULL) {

    $this->responder = $responder;
  }

  public function process($payment) {

    $validationHeader = "";
    $validationHeader .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $validationHeader .= "Content-Type: "
      . "application/x-www-form-urlencoded\r\n";
    $validationHeader .= "Content-Length: <contentlength>\r\n\r\n";

    $this->responder->create(['ipnVars' => $payment, 'validationHeader' => $validationHeader]);
  }
}
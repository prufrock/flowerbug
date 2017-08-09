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
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = 'ssl://www.paypal.com';
    $validationPort = 443;
    $validationTimeout = 30;
    $validationExpectedResponse = "VERIFIED";
    $invalidExpectedResponse = "INVALID";
    $ipnDataStore = new \stdClass();
    $logger = new \stdClass();

    $this->responder->create([
      'ipnVars' => $payment,
      'validationHeader' => $validationHeader,
      'validationCmd' => $validationCmd,
      'validationUrl' => $validationUrl,
      'validationPort' => $validationPort,
      'validationTimeout' => $validationTimeout,
      'validationExpectedResponse' => $validationExpectedResponse,
      'invalidExpectedResponse' => $invalidExpectedResponse,
      'ipnDataStore' => $ipnDataStore,
      'logger' => $logger
    ]);
  }
}
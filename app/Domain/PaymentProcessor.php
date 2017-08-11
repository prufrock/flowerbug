<?php namespace App\Domain;

use Illuminate\Support\Facades\Log;

class PaymentProcessor {

  private $responder;

  private $orderFullFiller;

  public function __construct($responder = NULL, $orderFullFiller = NULL) {

    $this->responder = $responder;
    $this->orderFullFiller = $orderFullFiller;
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

    if(!$this->responder->isVerified()){
      $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received but couldn't be verified. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return;
    }

    if(!$this->responder->isValid()){
      $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received but couldn't be validated. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return;
    }

    if($this->responder->hasBeenReceivedBefore()){
      $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message has been received before. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return;
    }

    $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
      . "an IPN message was received succesfully. The "
      . " message is " . $this->responder->get('txn_id') . ".");

    $this->responder->persist();
    $itemsPurchased = $this->responder->getItemsPurchased();
    if(empty($itemsPurchased)){
      $this->_recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received succesfully. The "
        . " message is " . $this->responder->get('txn_id') . ": no items were purchased.");
      return;
    }

    $this->orderFullFiller->fulfill($itemsPurchased, $this->responder->getBuyersEmailAddress());

    return true;
  }

  private function recordAMessageInTheLog($message) {
    Log::info($message);
  }
}
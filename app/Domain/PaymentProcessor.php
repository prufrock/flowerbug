<?php namespace App\Domain;

use Illuminate\Support\Facades\Log;

class PaymentProcessor {

  private $responder;

  private $orderFullFiller;

  private $project;

  public function __construct(
    \App\Domain\IpnResponder $responder,
    \App\Domain\OrderFullFiller $orderFullFiller,
    \App\Domain\Project $project
  ) {

    $this->responder = $responder;
    $this->orderFullFiller = $orderFullFiller;
    $this->project = $project;
  }

  public function process($payment) {

    $this->prepareResponderToVerify($payment);

    if(!$this->responder->isVerified()){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received but couldn't be verified. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return false;
    }

    if(!$this->responder->isValid()){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received but couldn't be validated. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return false;
    }

    if($this->responder->hasBeenReceivedBefore()){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message has been received before. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return false;
    }

    $this->log(__METHOD__ . ":" . __LINE__ . ":"
      . "an IPN message was received successfully. The "
      . " message is " . $this->responder->get('txn_id') . ".");

    $this->responder->persist();
    $itemsPurchased = $this->responder->getItemsPurchased();
    if(empty($itemsPurchased)){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received successfully. The "
        . " message is " . $this->responder->get('txn_id') . ": no items were purchased.");
      return;
    }

    $this->orderFullFiller->fulfill(
      $this->project->find($itemsPurchased),
      $this->responder->getBuyersEmailAddress()
    );

    return true;
  }
  
  private function prepareResponderToVerify($payment) {
    $validationHeader = "";
    $validationHeader .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $validationHeader .= "Content-Type: "
      . "application/x-www-form-urlencoded\r\n";
    $validationHeader .= "Content-Length: <contentlength>\r\n\r\n";
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = config('flowerbug.paypal.ipn_verify_url');
    $validationPort = config('flowerbug.paypal.ipn_verify_port');
    $validationTimeout = 30;
    $validationExpectedResponse = "VERIFIED";
    $invalidExpectedResponse = "INVALID";

    $this->responder->initialize([
      'ipnVars' => $payment,
      'validationHeader' => $validationHeader,
      'validationCmd' => $validationCmd,
      'validationUrl' => $validationUrl,
      'validationPort' => $validationPort,
      'validationTimeout' => $validationTimeout,
      'validationExpectedResponse' => $validationExpectedResponse,
      'invalidExpectedResponse' => $invalidExpectedResponse,
    ]);
  }

  private function log($message) {
    Log::info($message);
  }
}
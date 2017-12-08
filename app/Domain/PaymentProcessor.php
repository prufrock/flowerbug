<?php namespace App\Domain;

use Illuminate\Support\Facades\Log;

class PaymentProcessor {

  private $responder;

  private $orderFullFiller;

  private $project;

  public function __construct(
    \App\Domain\IpnResponder $responder,
    \App\Domain\OrderFullFiller $orderFullFiller,
  $project = NULL
  ) {

    $this->responder = $responder;
    $this->orderFullFiller = $orderFullFiller;
    $this->project = $project;
  }

  public function process($payment) {

    $validationHeader = "";
    $validationHeader .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $validationHeader .= "Content-Type: "
      . "application/x-www-form-urlencoded\r\n";
    $validationHeader .= "Content-Length: <contentlength>\r\n\r\n";
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = env('PAYPAL_IPN_VERIFY_URL');
    $validationPort = env('PAYPAL_IPN_VERIFY_PORT');
    $validationTimeout = 30;
    $validationExpectedResponse = "VERIFIED";
    $invalidExpectedResponse = "INVALID";
    $ipnDataStore = new \stdClass();
    $logger = new \stdClass();

    $this->responder->initialize([
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
      return false;
    }

    if(!$this->responder->isValid()){
      $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received but couldn't be validated. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return false;
    }

    if($this->responder->hasBeenReceivedBefore()){
      $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message has been received before. The "
        . " message is " . $this->responder->get('txn_id') . ".");
      return false;
    }

    $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
      . "an IPN message was received successfully. The "
      . " message is " . $this->responder->get('txn_id') . ".");

    $this->responder->persist();
    $itemsPurchased = $this->responder->getItemsPurchased();
    if(empty($itemsPurchased)){
      $this->recordAMessageInTheLog(__METHOD__ . ":" . __LINE__ . ":"
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

  private function recordAMessageInTheLog($message) {
    Log::info($message);
  }
}
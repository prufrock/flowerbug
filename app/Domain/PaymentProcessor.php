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
    
    $this->responder->initializeWithIpnConfig(
      $payment,
      new IpnConfig()
    );
  }

  private function log($message) {
    Log::info($message);
  }
}
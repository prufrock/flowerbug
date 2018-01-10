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

  public function process($ipnMessage) {

    if($this->isNotVerifiedWithPaypal($ipnMessage)){
      return false;
    }

    if($this->paymentHasBeenReceivedBefore($ipnMessage)){
      return false;
    }

    $this->log(__METHOD__ . ":" . __LINE__ . ": Verified IPN message {$this->responder->get('txn_id', $ipnMessage)}.");

    $this->responder->persist($ipnMessage);
    $itemsPurchased = $this->responder->getItemsPurchased($ipnMessage);
    if(empty($itemsPurchased)){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received successfully. The "
        . " message is " . $this->responder->get('txn_id', $ipnMessage) . ": no items were purchased.");
      return;
    }

    $this->orderFullFiller->fulfill(
      $this->project->find($itemsPurchased),
      $this->responder->getBuyersEmailAddress($ipnMessage)
    );

    return true;
  }
  
  private function log($message) {
    Log::info($message);
  }

  private function isNotVerifiedWithPaypal($ipnMessage) {
    
    if(!$this->responder->isVerified($ipnMessage)) {
      
      $this->log(
        __METHOD__ . ":" . __LINE__ . ": The IPN message couldn't be verified {$this->responder->get('txn_id', $ipnMessage)}."
      );
      
      return true;
    } else {
      return false;
    }
  }

  private function paymentHasBeenReceivedBefore($ipnMessage) {
    
    if ($this->responder->hasBeenReceivedBefore($ipnMessage)) {

      $this->log(
      __METHOD__ . ":" . __LINE__ . ": The IPN message has been received before {$this->responder->get('txn_id', $ipnMessage)}."
    );

      return true;
    } else {
      return false;
    }
  }
}
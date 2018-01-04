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

    if(!$this->responder->isVerified($payment)){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received but couldn't be verified. The "
        . " message is " . $this->responder->get('txn_id', $payment) . ".");
      return false;
    }

    if($this->responder->hasBeenReceivedBefore($payment)){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message has been received before. The "
        . " message is " . $this->responder->get('txn_id', $payment) . ".");
      return false;
    }

    $this->log(__METHOD__ . ":" . __LINE__ . ":"
      . "an IPN message was received successfully. The "
      . " message is " . $this->responder->get('txn_id', $payment) . ".");

    $this->responder->persist($payment);
    $itemsPurchased = $this->responder->getItemsPurchased($payment);
    if(empty($itemsPurchased)){
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "an IPN message was received successfully. The "
        . " message is " . $this->responder->get('txn_id', $payment) . ": no items were purchased.");
      return;
    }

    $this->orderFullFiller->fulfill(
      $this->project->find($itemsPurchased),
      $this->responder->getBuyersEmailAddress($payment)
    );

    return true;
  }
  
  private function log($message) {
    Log::info($message);
  }
}
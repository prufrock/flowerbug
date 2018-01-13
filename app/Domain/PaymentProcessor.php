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
    
    if (!$this->verifyIpnMessage($ipnMessage)) {
      return false;
    }

    if ($this->ipnMessageIsVerifiedButNoItemsWerePurchased($ipnMessage)){
      return true;
    }

    $this->orderFullFiller->fulfill(
      $this->project->find($this->getItemsPurchased($ipnMessage)),
      $this->responder->getBuyersEmailAddress($ipnMessage)
    );

    return true;
  }
  
  private function log($message) {
    Log::info($message);
  }
  
  private function getItemsPurchased($ipnMessage) {

    return $this->responder->getItemsPurchased($ipnMessage);
  }
  
  private function ipnMessageIsVerifiedButNoItemsWerePurchased($ipnMessage) {
    
    if (empty($this->getItemsPurchased($ipnMessage))) {
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "The IPN message was received successfully, but no items were purchased {$this->responder->get('txn_id', $ipnMessage)}.");
      return true;
    } else { 
      return false;
    }
  }

  private function verifyIpnMessage($ipnMessage) {
    
    return $this->responder->verifyIpnMessage($ipnMessage);
  }
}
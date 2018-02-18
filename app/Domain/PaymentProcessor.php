<?php namespace App\Domain;

use Illuminate\Support\Facades\Log;

class PaymentProcessor {

  private $orderFullFiller;

  private $project;

  public function __construct(
    \App\Domain\OrderFullFiller $orderFullFiller,
    \App\Domain\Project $project
  ) {

    $this->orderFullFiller = $orderFullFiller;
    $this->project = $project;
  }

  public function process($ipnMessage) {
    
    if (!$ipnMessage->verifyIpnMessage()) {
      return false;
    }

    if ($this->ipnMessageIsVerifiedButNoItemsWerePurchased($ipnMessage)){
      return true;
    }

    $this->orderFullFiller->fulfill(
      $this->project->find($this->getItemsPurchased($ipnMessage)),
      $ipnMessage->getBuyersEmailAddress()
    );

    return true;
  }
  
  private function log($message) {
    Log::info($message);
  }
  
  private function getItemsPurchased($ipnMessage) {
    
    return $ipnMessage->getItemsPurchased();
  }
  
  private function ipnMessageIsVerifiedButNoItemsWerePurchased($ipnMessage) {
    
    if (empty($this->getItemsPurchased($ipnMessage))) {
      $this->log(__METHOD__ . ":" . __LINE__ . ":"
        . "The IPN message was received successfully, but no items were purchased {$ipnMessage->get('txn_id')}.");
      return true;
    } else { 
      return false;
    }
  }
}
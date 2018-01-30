<?php namespace App\Domain;

use Illuminate\Support\Facades\Log;

class IpnResponder {

  private $ipnDataStore;

  private $verifier;

  public function __construct(
    \App\Domain\IpnDataStore $ipnDataStore,
    \App\Domain\IpnMessageVerifier $verifier
  ) {

    $this->ipnDataStore = $ipnDataStore;
    $this->verifier = $verifier;
  }

  public function verifyIpnMessage($ipnMessage) {

    if($this->ipnMessageisNotFromPaypal($ipnMessage)){
      return false;
    }

    if($this->ipnMessageHasBeenReceivedBefore($ipnMessage)){
      return false;
    }

    $this->saveIpnMessage($ipnMessage);
    
    Log::info(__METHOD__ . ":" . __LINE__ . ": Verified IPN message {$this->get('txn_id', $ipnMessage)}.");

    return true;
  }
  
  private function hasBeenReceivedBefore($ipnMessage) {

    return $this->ipnDataStore->doesMessageExist($ipnMessage);
  }

  public function get($key, $ipnMessage) {
    
    return $ipnMessage[$key];
  }

  public function getItemsPurchased($ipnMessage) {

    $items = array();
    if(!is_array($ipnMessage)){
      return $items;
    }
    $findItemKeys = function($key, $value) {
      if(strpos($key, "item_number") !== FALSE){
        return $value;
      }
    };
    $items = array_filter(
      array_map( $findItemKeys
        , array_keys($ipnMessage)
        , array_values($ipnMessage)
      )
    );
    return $items;
  }

  private function isVerified($ipnMessage) {

    return $this->verifier->compute($ipnMessage);
  }

  private function ipnMessageIsNotFromPaypal($ipnMessage) {

    if(!$this->isVerified($ipnMessage)) {

      Log::info(
        __METHOD__ . ":" . __LINE__ . ": The IPN message couldn't be verified {$this->get('txn_id', $ipnMessage)}."
      );

      return true;
    } else {
      return false;
    }
  }

  private function ipnMessageHasBeenReceivedBefore($ipnMessage) {

    if ($this->hasBeenReceivedBefore($ipnMessage)) {

      Log::info(
        __METHOD__ . ":" . __LINE__ . ": The IPN message has been received before {$this->get('txn_id', $ipnMessage)}."
      );

      return true;
    } else {
      return false;
    }
  }

  private function saveIpnMessage($ipnMessage) {

    return $this->ipnDataStore->storeMessage($ipnMessage);
  }

}
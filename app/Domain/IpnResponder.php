<?php namespace App\Domain;

use Illuminate\Support\Facades\Log;

class IpnResponder {

  private $fproxy;

  private $ipnDataStore;

  private $ipnConfig;

  private $verifierFactory;

  public function __construct(
    $fproxy = null,
    \App\Domain\IpnDataStore $ipnDataStore,
    \App\Domain\IpnMessageVerifierFactory $verifierFactory
  ) {

    $this->fproxy = $fproxy;
    $this->ipnDataStore = $ipnDataStore;
    $this->ipnConfig = new IpnConfig();
    $this->verifierFactory = $verifierFactory;
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
  
  private function getFproxy() {
    
    return $this->fproxy;
  }
  
  private function getIpnConfig() {
    
    return $this->ipnConfig;
  }
  
  public function hasBeenReceivedBefore($ipnMessage) {

    return $this->ipnDataStore->doesMessageExist($ipnMessage);
  }

  public function persist($ipnMessage) {

    return $this->ipnDataStore->storeMessage($ipnMessage);
  }

  public function get($key, $ipnMessage) {

    return $ipnMessage[$key];
  }

  public function getBuyersEmailAddress($ipnMessage) {

    return array_get($ipnMessage, 'payer_email', '');
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

    return $this->verifierFactory->create($this)->compute($ipnMessage);
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

    $this->persist($ipnMessage);
  }

}
<?php namespace App\Domain;

use Illuminate\Support\Facades\Log;

class IpnResponder {

  private $fproxy;

  private $ipnVars;

  private $ipnDataStore;

  private $ipnConfig;

  public function __construct(
    \App\Domain\FilePointerProxy $fproxy,
    \App\Domain\IpnDataStore $ipnDataStore
  ) {

    $this->fproxy = $fproxy;
    $this->ipnDataStore = $ipnDataStore;
    $this->ipnConfig = new IpnConfig();
  }

  public function isVerified($ipnMessage) {

    $errno = null;
    $errstr = null;

    // read the post from PayPal system and add 'cmd'
    $req = $this->ipnConfig->getCmd();

    foreach ($ipnMessage as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
    }

    $header = "POST " . config('flowerbug.paypal.ipn_verify_resource') . " HTTP/1.1\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n";
    $header .= "Host: " . env('PAYPAL_IPN_VERIFY_HOST') . "\r\n";
    $header .= "Connection: close\r\n\r\n";
    
    $fp = $this->fproxy->fsockopen(
      $this->ipnConfig->getUrl(),
      $this->ipnConfig->getPort(),
      $errno,
      $errstr,
      $this->ipnConfig->getTimeout()
    );

    if (!$fp) {
      return false;
    } else {
      $this->fproxy->fputs($fp, $header . $req);
      while (!$this->fproxy->feof($fp)) {
        $res = $this->fproxy->fgets($fp, 1024);
        if (strcmp(trim($res), $this->ipnConfig->getValidatedResponse()) == 0) {
          $this->fproxy->fclose($fp);
          return true;
        } elseif (strcmp(trim($res), $this->ipnConfig->getInvalidatedResponse()) == 0)  {
          $this->fproxy->fclose($fp);
          return false;
        }
      }
    }
    
    $this->fproxy->fclose($fp);
    return false;
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
}
<?php namespace App\Domain;

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

  public function initialize($ipnVars) {
    
    $this->ipnVars = $ipnVars;
  }

  public function isVerified($ipnVars) {

    $errno = null;
    $errstr = null;

    // read the post from PayPal system and add 'cmd'
    $req = $this->ipnConfig->getCmd();

    foreach ($ipnVars as $key => $value) {
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

  public function isValid($ipnVars) {

    return true;
  }

  public function hasBeenReceivedBefore($ipnVars) {

    return $this->ipnDataStore->doesMessageExist($ipnVars);
  }

  public function persist($ipnVars) {

    return $this->ipnDataStore->storeMessage($ipnVars);
  }

  public function get($key, $ipnVars) {

    return $ipnVars[$key];
  }

  public function getBuyersEmailAddress($ipnVars) {

    return array_get($ipnVars, 'payer_email', '');
  }

  public function getItemsPurchased($ipnVars) {

    $items = array();
    if(!is_array($ipnVars)){
      return $items;
    }
    $findItemKeys = function($key, $value) {
      if(strpos($key, "item_number") !== FALSE){
        return $value;
      }
    };
    $items = array_filter(
      array_map( $findItemKeys
        , array_keys($ipnVars)
        , array_values($ipnVars)
      )
    );
    return $items;
  }
}
<?php namespace App\Domain;

class IpnResponder {

  private $fproxy;

  private $validationUrl;

  private $validationPort;

  private $validationTimeout;

  private $validationCmd;

  private $ipnVars;

  private $validationExpectedResponse;
  
  private $invalidExpectedResponse;

  private $ipnDataStore;

  public function __construct(
    \App\Domain\FilePointerProxy $fproxy,
    \App\Domain\IpnDataStore $ipnDataStore
  ) {

    $this->fproxy = $fproxy;
    $this->ipnDataStore = $ipnDataStore;
  }

  public function initialize($ipnVars, IpnConfig $ipnConfig) {

    $this->validationUrl = $ipnConfig->getUrl();
    $this->validationPort = $ipnConfig->getPort();
    $this->validationTimeout = $ipnConfig->getTimeout();
    $this->validationCmd = $ipnConfig->getCmd();
    $this->validationExpectedResponse = $ipnConfig->getValidatedResponse();
    $this->invalidExpectedResponse = $ipnConfig->getInvalidatedResponse();
    $this->ipnVars = $ipnVars;
  }

  public function isVerified() {

    $errno = null;
    $errstr = null;

    // read the post from PayPal system and add 'cmd'
    $req = $this->validationCmd;

    foreach ($this->ipnVars as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
    }

    $header = "POST " . config('flowerbug.paypal.ipn_verify_resource') . " HTTP/1.1\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n";
    $header .= "Host: " . env('PAYPAL_IPN_VERIFY_HOST') . "\r\n";
    $header .= "Connection: close\r\n\r\n";
    
    $fp = $this->fproxy->fsockopen(
      $this->validationUrl,
      $this->validationPort,
      $errno,
      $errstr,
      $this->validationTimeout     
    );

    if (!$fp) {
      return false;
    } else {
      $this->fproxy->fputs($fp, $header . $req);
      while (!$this->fproxy->feof($fp)) {
        $res = $this->fproxy->fgets($fp, 1024);
        if (strcmp(trim($res), $this->validationExpectedResponse) == 0) {
          $this->fproxy->fclose($fp);
          return true;
        } elseif (strcmp(trim($res), $this->invalidExpectedResponse) == 0)  {
          $this->fproxy->fclose($fp);
          return false;
        }
      }
    }
    
    $this->fproxy->fclose($fp);
    return false;
  }

  public function isValid() {

    return true;
  }

  public function hasBeenReceivedBefore() {

    return $this->ipnDataStore->doesMessageExist($this->ipnVars);
  }

  public function persist() {

    return $this->ipnDataStore->storeMessage($this->ipnVars);
  }

  public function get($key) {

    return $this->ipnVars[$key];
  }

  public function getBuyersEmailAddress() {

    return array_get($this->ipnVars, 'payer_email', '');
  }

  public function getItemsPurchased() {

    $items = array();
    if(!is_array($this->ipnVars)){
      return $items;
    }
    $findItemKeys = function($key, $value) {
      if(strpos($key, "item_number") !== FALSE){
        return $value;
      }
    };
    $items = array_filter(
      array_map( $findItemKeys
        , array_keys($this->ipnVars)
        , array_values($this->ipnVars)
      )
    );
    return $items;
  }
}
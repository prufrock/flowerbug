<?php namespace App\Domain;

class IpnMessageVerifier {

  private $fproxy;

  private $ipnConfig;

  public function __construct($fproxy = null) {

    $this->fproxy = $fproxy;
    $this->ipnConfig = new IpnConfig();
  }
  
  public function create() {
    
    return new IpnMessageVerifier($this->fproxy);
  }

  public function compute($ipnMessage) {

    $errno = null;
    $errstr = null;

    // read the post from PayPal system and add 'cmd'
    $req = $this->getIpnConfig()->getCmd();

    foreach ($ipnMessage as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
    }

    $header = "POST " . config('flowerbug.paypal.ipn_verify_resource') . " HTTP/1.1\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n";
    $header .= "Host: " . env('PAYPAL_IPN_VERIFY_HOST') . "\r\n";
    $header .= "Connection: close\r\n\r\n";

    $fp = $this->getFproxy()->fsockopen(
      $this->getIpnConfig()->getUrl(),
      $this->getIpnConfig()->getPort(),
      $errno,
      $errstr,
      $this->getIpnConfig()->getTimeout()
    );

    if (!$fp) {
      return false;
    } else {
      $this->getFproxy()->fputs($fp, $header . $req);
      while (!$this->getFproxy()->feof($fp)) {
        $res = $this->getFproxy()->fgets($fp, 1024);
        if (strcmp(trim($res), $this->getIpnConfig()->getValidatedResponse()) == 0) {
          $this->getFproxy()->fclose($fp);
          return true;
        } elseif (strcmp(trim($res), $this->getIpnConfig()->getInvalidatedResponse()) == 0)  {
          $this->getFproxy()->fclose($fp);
          return false;
        }
      }
    }

    $this->getFproxy()->fclose($fp);
    return false;
  }
  
  private function getIpnConfig() {
    
    return $this->ipnConfig;
  }
  
  private function getFproxy() {
    
    return $this->fproxy;
  }
}
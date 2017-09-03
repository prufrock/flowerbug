<?php namespace App\Domain;

class IpnResponder {

  private $fproxy;

  private $validationUrl;

  private $validationPort;

  private $validationTimeout;

  private $validationCmd;

  private $ipnVars;

  private $validationExpectedResponse;

  private $ipnDataStore;

  public function __construct(
    \App\Domain\FilePointerProxy $fproxy,
    \App\Domain\IpnDataStore $ipnDataStore
  ) {

    $this->fproxy = $fproxy;
    $this->ipnDataStore = $ipnDataStore;
  }

  public function initialize($arguments) {

    $this->validationUrl = $arguments['validationUrl'];
    $this->validationPort = $arguments['validationPort'];
    $this->validationTimeout = $arguments['validationTimeout'];
    $this->validationCmd = $arguments['validationCmd'];
    $this->validationExpectedResponse = $arguments['validationExpectedResponse'];
    $this->ipnVars = $arguments['ipnVars'];
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

    $header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n";
    $header .= "Host: www.paypal.com\r\n";
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
        if (strcmp($res, $this->validationExpectedResponse) == 0) {
          $this->fproxy->fclose(true);
          return true;
        } else {
          $this->fproxy->fclose(true);
          return false;
        }
      }
    }
  }

  public function isValid() {
    return true;
  }

  public function hasBeenReceivedBefore($ipnVars) {
    return $this->ipnDataStore->doesMessageExist($ipnVars);
  }

  public function persist() {

    return $this->ipnDataStore->storeMessage(['txn_id' => 1]);
  }
}
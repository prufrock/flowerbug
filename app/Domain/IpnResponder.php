<?php namespace App\Domain;

class IpnResponder {

  private $ipnVars;
  private $validationHeader;
  private $validationCmd;
  private $validationUrl;
  private $validationPort;
  private $validationTimeout;
  private $validationExpectedResponse;
  private $invalidExpectedResponse;
  private $ipnDataStore;
  private $logger;

  public function __construct(\App\Domain\FilePointerProxy $fproxy) {

  }

  public function create($params) {

    foreach($params as $key => $value) {
      $this->$key = $value;
    }
  }

  public function isVerified() {

    return true;
  }
}
<?php namespace App\Domain;

class IpnResponder {

  private $fproxy;

  private $validationUrl;

  private $validationPort;

  private $validationTimeout;

  public function __construct(\App\Domain\FilePointerProxy $fproxy) {

    $this->fproxy = $fproxy;
  }

  public function initialize($arguments) {

    $this->validationUrl = $arguments['validationUrl'];
    $this->validationPort = $arguments['validationPort'];
    $this->validationTimeout = $arguments['validationTimeout'];
  }

  public function isVerified() {

    $errno = null;
    $errstr = null;

    $this->fproxy->fsockopen(
      $this->validationUrl,
      $this->validationPort,
      $errno,
      $errstr,
      $this->validationTimeout
    );

    return true;
  }
}
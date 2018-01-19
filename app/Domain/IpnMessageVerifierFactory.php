<?php namespace App\Domain;

class IpnMessageVerifierFactory implements IpnMessageVerifierFactoryInterface {

  private $fproxy;

  public function __construct(\App\Domain\FilePointerProxy $fproxy) {

    $this->fproxy = $fproxy;
  }

  public function create() {

    return new IpnMessageVerifier($this->fproxy);
  }
}

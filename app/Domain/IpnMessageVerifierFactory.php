<?php namespace App\Domain;

class IpnMessageVerifierFactory {

  private $fproxy;

  public function __construct(
    \App\Domain\FilePointerProxy $fproxy
  ) {

    $this->fproxy = $fproxy;
  }

  public function create(
    $responder,
    $fproxy=null,
    $ipnConfig=null
  ) {

    return new IpnMessageVerifier($responder, $this->fproxy, new IpnConfig());
  }
}

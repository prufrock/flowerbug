<?php namespace Tests\Unit\Domain;

use App\Domain\IpnMessageVerifierFactory;
use Tests\TestCase;

class IpnMessageVerifierFactoryTest extends TestCase {

  public function testNew() {

    $responder = $this->app->make(\App\Domain\IpnMessageVerifierFactory::class);

    $this->assertInstanceOf(
      \App\Domain\IpnMessageVerifierFactory::class,
      $responder
    );
  }
  
  public function testHasCreateMethod() {

    $responder = $this->app->make(\App\Domain\IpnMessageVerifierFactory::class);

    $this->assertTrue(
      (new \ReflectionObject($responder))->hasMethod('create'),
      "IpnMessageVerifierFactory doesn't have a create method."
    );
  }

  public function testCreate() {

    $fproxy = app(\App\Domain\FilePointerProxy::class);
    $ipnDataStore = app(\App\Domain\IpnDataStore::class);
    $factory = new IpnMessageVerifierFactory($fproxy);
    $responder = new \App\Domain\IpnResponder(
      null,
      $ipnDataStore,
      new IpnMessageVerifierFactory($fproxy)
    );
    
    $this->assertInstanceOf(\App\Domain\IpnMessageVerifier::class, $factory->create($responder));
  }
}

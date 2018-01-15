<?php namespace Tests\Unit\Domain;

use App\Domain\IpnResponder;
use Tests\TestCase;
use Mockery as m;

class IpnResponderTest extends TestCase {

  public function testNew() {

    $responder = $this->app->make(\App\Domain\IpnResponder::class);

    $this->assertInstanceOf(
      \App\Domain\IpnResponder::class,
      $responder
    );
  }
  
  public function testGetFproxyExists() {
    
    $responder = $this->app->make(\App\Domain\IpnResponder::class);

    $this->assertTrue(
      (new \ReflectionObject($responder))->hasMethod('getFproxy'),
      "IpnResponder doesn't have a getFproxy method."
    );
  }

  public function testGetIpnConfigExists() {

    $responder = $this->app->make(\App\Domain\IpnResponder::class);

    $this->assertTrue(
      (new \ReflectionObject($responder))->hasMethod('getIpnConfig'),
      "IpnResponder doesn't have a getIpnConfig method."
    );
  }

  public function testIsVerified() {

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier->shouldReceive('compute')->andReturn(true);
    $verifierFactory->shouldReceive('create')->andReturn($verifier);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertTrue($responder->isVerified(['txn_id' => 1]));
  }

  public function testHasBeenReceivedBefore() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(true)->once();
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertTrue($responder->hasBeenReceivedBefore(['txn_id' => 1]));
  }

  public function testPersist() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertTrue($responder->persist(['txn_id' => 1]));
  }

  public function testGet() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertEquals(1, $responder->get('txn_id', ['txn_id' => '1']));
  }

  public function testGetBuyersEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertEquals(
      'buyer@example.com',
      $responder->getBuyersEmailAddress(['payer_email' => 'buyer@example.com'])
    );
  }

  public function testGetBuyersEmailAddressWithNoEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertNotNull($responder->getBuyersEmailAddress(['payer_email' => '']));
    $this->assertEquals('', $responder->getBuyersEmailAddress(['payer_email' => '']));
  }

  public function testGetItemsPurchased() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertEquals(
      ['technique201707', 'technique201708'],
      $responder->getItemsPurchased(
        [
          'item_number_1' => 'technique201707',
          'item_number_2' => 'technique201708'
        ]
      )
    );
  }
  
  public function testVerifyValidIpnMessage() {

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(false)->once();
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier->shouldReceive('compute')->andReturn(true);
    $verifierFactory->shouldReceive('create')->andReturn($verifier);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);
    
    $this->assertTrue($responder->verifyIpnMessage(['txn_id' => 1]));
  }

  public function testVerifyInvalidIpnMessage() {

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier->shouldReceive('compute')->andReturn(false);
    $verifierFactory->shouldReceive('create')->andReturn($verifier);
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertFalse($responder->verifyIpnMessage(['txn_id' => 1]));
  }

  public function testVerifyValidIpnMessageThatHasBeenReceivedBefore() {

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $verifierFactory = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier = m::mock(\App\Domain\IpnMessageVerifier::class);
    $verifier->shouldReceive('compute')->andReturn(true);
    $verifierFactory->shouldReceive('create')->andReturn($verifier);
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(true)->once();
    $responder = new IpnResponder($fproxy, $ipnDataStore, $verifierFactory);

    $this->assertFalse($responder->verifyIpnMessage(['txn_id' => 1]));
  }
}

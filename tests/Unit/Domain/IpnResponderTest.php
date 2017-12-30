<?php namespace Tests\Unit\Domain;

use App\Domain\IpnResponder;
use Tests\TestCase;
use Mockery as m;

class IpnResponderTest extends TestCase {

  public function testNew() {

    $this->app->bind('\App\Domain\FilePointerProxy', m::mock('\App\Domain\FilePointerProxy'));
    $responder = $this->app->make(\App\Domain\IpnResponder::class);

    $this->assertInstanceOf(
      \App\Domain\IpnResponder::class,
      $responder
    );
  }

  public function testIsVerified() {

    $ipnVars = ['txn_id' => 1];
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = config('flowerbug.paypal.ipn_verify_url');
    $validationPort = config('flowerbug.paypal.ipn_verify_port');
    $validationTimeout = 30;
    $validationExpectedResponse = "VERIFIED";
    $errno = null;
    $errstr = null;

    $req = $validationCmd;
    foreach ($ipnVars as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
    }

    $header ="POST " . config('flowerbug.paypal.ipn_verify_resource') . " HTTP/1.1\r\n";
    $header .="Content-Type: application/x-www-form-urlencoded\r\n";
    $header .="Content-Length: " . strlen($req) . "\r\n";
    $header .="Host: " . config('flowerbug.paypal.ipn_verify_host') . "\r\n";
    $header .="Connection: close\r\n\r\n";
    $header .= $req;
    
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $fproxy->shouldReceive('fsockopen')
      ->with(
        $validationUrl,
        $validationPort,
        $errno,
        $errstr,
        $validationTimeout
      )->andReturn(true)->once();
    $fproxy->shouldReceive('fputs')->with(
      true,
      $header
    )->once();
    $response = tmpfile();
    fwrite($response, 'VERIFIED');
    fseek($response, 0);
    $fproxy->shouldReceive('feof')->andReturn(false)->once();
    $fproxy->shouldReceive('fgets')->with(true, 1024)->andReturn('VERIFIED')->once();
    $fproxy->shouldReceive('fclose')->with($response)->once();
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $responder->initialize(
      [
        'ipnVars' => $ipnVars,
        'validationUrl' => $validationUrl,
        'validationPort' => $validationPort,
        'validationTimeout' => $validationTimeout,
        'validationCmd' => $validationCmd,
        'validationExpectedResponse' => $validationExpectedResponse,
        'invalidExpectedResponse' => ''
      ]
    );

    $this->assertTrue($responder->isVerified());
  }

  public function testIsVerifiedUnableToGetAFileHandle() {

    $errno = null;
    $errstr = null;

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $fproxy->shouldReceive('fsockopen')
      ->with(
        config('flowerbug.paypal.ipn_verify_url'),
        $validationPort = config('flowerbug.paypal.ipn_verify_port'),
        $errno,
        $errstr,
        30
      )->andReturn(false)->once();
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $responder->initialize(
      [
        'ipnVars' => ['txn_id' => 1],
        'validationUrl' => config('flowerbug.paypal.ipn_verify_url'),
        'validationPort' => config('flowerbug.paypal.ipn_verify_port'),
        'validationTimeout' => 30,
        'validationCmd' => '',
        'validationExpectedResponse' => '',
        'invalidExpectedResponse' => ''
      ]
    );

    $this->assertFalse($responder->isVerified());
  }

  public function testIsVerifiedInvalidReponse() {

    $errno = null;
    $errstr = null;

    $req = 'cmd=_notify-validate';
    foreach (['txn_id' => 1] as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
    }

    $header ="POST " . config('flowerbug.paypal.ipn_verify_resource') . " HTTP/1.1\r\n";
    $header .="Content-Type: application/x-www-form-urlencoded\r\n";
    $header .="Content-Length: " . strlen($req) . "\r\n";
    $header .="Host: " . config('flowerbug.paypal.ipn_verify_host') . "\r\n";
    $header .="Connection: close\r\n\r\n";
    $header .= $req;

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $fproxy->shouldReceive('fsockopen')
      ->with(
        config('flowerbug.paypal.ipn_verify_url'),
        config('flowerbug.paypal.ipn_verify_port'),
        $errno,
        $errstr,
        30
      )->andReturn(true)->once();
    $fproxy->shouldReceive('fputs')->with(
      true,
      $header
    )->once();
    $response = tmpfile();
    fwrite($response, 'INVALID');
    fseek($response, 0);
    $fproxy->shouldReceive('feof')->andReturn(false)->once();
    $fproxy->shouldReceive('fgets')->with(true, 1024)->andReturn('INVALID')->once();
    $fproxy->shouldReceive('fclose')->with($response)->once();
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $responder->initialize(
      [
        'ipnVars' => ['txn_id' => 1],
        'validationUrl' => config('flowerbug.paypal.ipn_verify_url'),
        'validationPort' => config('flowerbug.paypal.ipn_verify_port'),
        'validationTimeout' => 30,
        'validationCmd' => 'cmd=_notify-validate',
        'validationExpectedResponse' => 'VERIFIED',
        'invalidExpectedResponse' => 'INVALID'
      ]
    );

    $this->assertFalse($responder->isVerified());
  }

  public function testIsValid() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertTrue($responder->isValid());
  }

  public function testHasBeenReceivedBefore() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(true)->once();
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $responder->initialize(
      [
        'ipnVars' => ['txn_id' => 1],
        'validationUrl' => '',
        'validationPort' => 0,
        'validationTimeout' => 0,
        'validationCmd' => '',
        'validationExpectedResponse' => '',
        'invalidExpectedResponse' => ''
      ]
    );

    $this->assertTrue($responder->hasBeenReceivedBefore());
  }

  public function testPersist() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $responder->initialize(
      [
        'ipnVars' => ['txn_id' => 1],
        'validationUrl' => '',
        'validationPort' => 0,
        'validationTimeout' => 0,
        'validationCmd' => '',
        'validationExpectedResponse' => '',
        'invalidExpectedResponse' => ''
      ]
    );

    $this->assertTrue($responder->persist());
  }

  public function testGet() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);
    $responder->initialize(
      [
        'ipnVars' => ['txn_id' => 1],
        'validationUrl' => '',
        'validationPort' => 0,
        'validationTimeout' => 0,
        'validationCmd' => '',
        'validationExpectedResponse' => '',
        'invalidExpectedResponse' => ''
      ]
    );

    $this->assertEquals(1, $responder->get('txn_id'));
  }

  public function testGetBuyersEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);
    $responder->initialize(
      [
        'ipnVars' => ['payer_email' => 'buyer@example.com'],
        'validationUrl' => '',
        'validationPort' => 0,
        'validationTimeout' => 0,
        'validationCmd' => '',
        'validationExpectedResponse' => '',
        'invalidExpectedResponse' => ''
      ]
    );

    $this->assertEquals('buyer@example.com', $responder->getBuyersEmailAddress());
  }

  public function testGetBuyersEmailAddressWithNoEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertNotNull($responder->getBuyersEmailAddress());
    $this->assertEquals('', $responder->getBuyersEmailAddress());
  }

  public function testGetItemsPurchased() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);
    $responder->initialize(
      [
        'ipnVars' => ['item_number_1' => 'technique201707', 'item_number_2' => 'technique201708'],
        'validationUrl' => '',
        'validationPort' => 0,
        'validationTimeout' => 0,
        'validationCmd' => '',
        'validationExpectedResponse' => '',
        'invalidExpectedResponse' => ''
      ]
    );

    $this->assertEquals(['technique201707', 'technique201708'], $responder->getItemsPurchased());
  }
  
  public function testInitializeWithIpnConfig() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);
    
    try {
      $responder->initializeWithIpnConfig(
        ['item_number_1' => 'technique201707'],
        new \App\Domain\IpnConfig()
      );
    } catch(\Exception $e) {
      $this->fail('InitializeWithIpnConfig didn\'t work with the parameters passed.');
    }
    
    $this->assertTrue(true);
  }
}

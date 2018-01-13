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

    $this->assertTrue($responder->isVerified($ipnVars));
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

    $this->assertFalse($responder->isVerified(['txn_id' => 1]));
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

    $this->assertFalse($responder->isVerified(['txn_id' => 1]));
  }

  public function testHasBeenReceivedBefore() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(true)->once();
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertTrue($responder->hasBeenReceivedBefore(['txn_id' => 1]));
  }

  public function testPersist() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertTrue($responder->persist(['txn_id' => 1]));
  }

  public function testGet() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertEquals(1, $responder->get('txn_id', ['txn_id' => '1']));
  }

  public function testGetBuyersEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertEquals(
      'buyer@example.com',
      $responder->getBuyersEmailAddress(['payer_email' => 'buyer@example.com'])
    );
  }

  public function testGetBuyersEmailAddressWithNoEmailAddress() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertNotNull($responder->getBuyersEmailAddress(['payer_email' => '']));
    $this->assertEquals('', $responder->getBuyersEmailAddress(['payer_email' => '']));
  }

  public function testGetItemsPurchased() {

    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

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

    $ipnVars = ['txn_id' => 1];
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = config('flowerbug.paypal.ipn_verify_url');
    $validationPort = config('flowerbug.paypal.ipn_verify_port');
    $validationTimeout = 30;
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
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(false)->once();
    $responder = new IpnResponder($fproxy, $ipnDataStore); 
    
    $this->assertTrue($responder->verifyIpnMessage($ipnVars));
  }

  public function testVerifyInvalidIpnMessage() {

    $ipnVars = ['txn_id' => 1];
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = config('flowerbug.paypal.ipn_verify_url');
    $validationPort = config('flowerbug.paypal.ipn_verify_port');
    $validationTimeout = 30;
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
    $fproxy->shouldReceive('fgets')->with(true, 1024)->andReturn('INVALID')->once();
    $fproxy->shouldReceive('fclose')->with($response)->once();
    $ipnDataStore = m::mock('\App\Domain\IpnDataStore');
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertFalse($responder->verifyIpnMessage($ipnVars));
  }

  public function testVerifyValidIpnMessageThatHasBeenReceivedBefore() {

    $ipnVars = ['txn_id' => 1];
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = config('flowerbug.paypal.ipn_verify_url');
    $validationPort = config('flowerbug.paypal.ipn_verify_port');
    $validationTimeout = 30;
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
    $ipnDataStore->shouldReceive('storeMessage')->with(['txn_id' => 1])->andReturn(true);
    $ipnDataStore->shouldReceive('doesMessageExist')->with(['txn_id' => 1])->andReturn(true)->once();
    $responder = new IpnResponder($fproxy, $ipnDataStore);

    $this->assertfalse($responder->verifyIpnMessage($ipnVars));
  }
}

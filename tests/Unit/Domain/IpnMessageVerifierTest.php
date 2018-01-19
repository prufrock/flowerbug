<?php namespace Tests\Unit\Domain;

use App\Domain\IpnMessageVerifier;
use Tests\TestCase;
use Mockery as m;

class IpnMessageVerifierTest extends TestCase {
  
  public function testComputeExists() {

    $verifier = new IpnMessageVerifier();
    
    $this->assertTrue(
      (new \ReflectionObject($verifier))->hasMethod('compute'),
      "IpnMessageVerifier doesn't have a compute method."
    );
  }
  
  public function testHasCreateMethod() {

    $verifier = new IpnMessageVerifier();

    $this->assertTrue(
      (new \ReflectionObject($verifier))->hasMethod('create'),
      "IpnMessageVerifier doesn't have a create method."
    );
  }
  
  public function testVerifyAValidMessage() {

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

    $verifier = new IpnMessageVerifier($fproxy);
    
    $this->assertTrue($verifier->compute(['txn_id' => 1]));
  }
  
  public function testVerifyAnInvalidMessage() {

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
    
    $verifier = new IpnMessageVerifier($fproxy);

    $this->assertTrue($verifier->compute(['txn_id' => 1]));
  }
  
  public function testComputeIsUnableToGetAFileHandle() {

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

    $verifier = new IpnMessageVerifier($fproxy);

    $this->assertFalse($verifier->compute(['txn_id' => 1]));
  }

}

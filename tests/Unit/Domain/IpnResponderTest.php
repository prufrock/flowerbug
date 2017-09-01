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
    $validationUrl = 'ssl://www.paypal.com';
    $validationPort = 443;
    $validationTimeout = 30;
    $validationExpectedResponse = "VERIFIED";
    $errno = null;
    $errstr = null;

    $req = $validationCmd;
    foreach ($ipnVars as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
    }

    $header ="POST /cgi-bin/webscr HTTP/1.1\r\n";
    $header .="Content-Type: application/x-www-form-urlencoded\r\n";
    $header .="Content-Length: " . strlen($req) . "\r\n";
    $header .="Host: www.paypal.com\r\n";
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
    $fproxy->shouldReceive('feof')->andReturn(false)->once();
    $fproxy->shouldReceive('fgets')->with(true, 1024)->andReturn("VERIFIED")->once();
    $fproxy->shouldReceive('fclose')->with(true)->once();

    $responder = new IpnResponder($fproxy);

    $responder->initialize(
      [
        'ipnVars' => $ipnVars,
        'validationUrl' => $validationUrl,
        'validationPort' => $validationPort,
        'validationTimeout' => $validationTimeout,
        'validationCmd' => $validationCmd,
        'validationExpectedResponse' => $validationExpectedResponse
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
        'ssl://www.paypal.com',
        443,
        $errno,
        $errstr,
        30
      )->andReturn(false)->once();

    $responder = new IpnResponder($fproxy);

    $responder->initialize(
      [
        'ipnVars' => ['txn_id' => 1],
        'validationUrl' => 'ssl://www.paypal.com',
        'validationPort' => 443,
        'validationTimeout' => 30,
        'validationCmd' => 'cmd=_notify-validate',
        'validationExpectedResponse' => "VERIFIED"
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

    $header ="POST /cgi-bin/webscr HTTP/1.1\r\n";
    $header .="Content-Type: application/x-www-form-urlencoded\r\n";
    $header .="Content-Length: " . strlen($req) . "\r\n";
    $header .="Host: www.paypal.com\r\n";
    $header .="Connection: close\r\n\r\n";
    $header .= $req;

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $fproxy->shouldReceive('fsockopen')
      ->with(
        'ssl://www.paypal.com',
        443,
        $errno,
        $errstr,
        30
      )->andReturn(true)->once();
    $fproxy->shouldReceive('fputs')->with(
      true,
      $header
    )->once();
    $fproxy->shouldReceive('feof')->andReturn(false)->once();
    $fproxy->shouldReceive('fgets')->with(true, 1024)->andReturn("INVALID")->once();
    $fproxy->shouldReceive('fclose')->with(true)->once();

    $responder = new IpnResponder($fproxy);

    $responder->initialize(
      [
        'ipnVars' => ['txn_id' => 1],
        'validationUrl' => 'ssl://www.paypal.com',
        'validationPort' => 443,
        'validationTimeout' => 30,
        'validationCmd' => 'cmd=_notify-validate',
        'validationExpectedResponse' => "VERIFIED"
      ]
    );

    $this->assertFalse($responder->isVerified());
  }
}

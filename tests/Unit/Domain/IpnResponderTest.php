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

    $payment = ['txn_id' => 1];
    $validationHeader = "";
    $validationHeader .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $validationHeader .= "Content-Type: "
      . "application/x-www-form-urlencoded\r\n";
    $validationHeader .= "Content-Length: <contentlength>\r\n\r\n";
    $validationCmd = 'cmd=_notify-validate';
    $validationUrl = 'ssl://www.paypal.com';
    $validationPort = 443;
    $validationTimeout = 30;
    $validationExpectedResponse = "VERIFIED";
    $invalidExpectedResponse = "INVALID";
    $ipnDataStore = new \stdClass();
    $logger = new \stdClass();
    $errno = null;
    $errstr = null;

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $fproxy->shouldReceive('fsockopen')
      ->with(
        $validationUrl,
        $validationPort,
        $errno,
        $errstr,
        $validationTimeout
      )->once();

    $responder = new IpnResponder($fproxy);

    $responder->initialize([
      'validationUrl' => $validationUrl,
      'validationPort' => $validationPort,
      'validationTimeout' => $validationTimeout
    ]);

    $this->assertTrue($responder->isVerified());
  }
}

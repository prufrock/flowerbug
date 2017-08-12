<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class IpnResponderTest extends TestCase {

  private function containerOverride($fullClassName, $override) {
    $this->app->bind($fullClassName, $override);
  }

  public function testNew() {

    $this->containerOverride('\App\Domain\FilePointerProxy', m::mock('\App\Domain\FilePointerProxy'));
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

    $fproxy = m::mock('\App\Domain\FilePointerProxy');
    $this->containerOverride('\App\Domain\FilePointerProxy', $fproxy);

    $responder = $this->app->make(\App\Domain\IpnResponder::class);

    $responder->create([
      'ipnVars' => $payment,
      'validationHeader' => $validationHeader,
      'validationCmd' => $validationCmd,
      'validationUrl' => $validationUrl,
      'validationPort' => $validationPort,
      'validationTimeout' => $validationTimeout,
      'validationExpectedResponse' => $validationExpectedResponse,
      'invalidExpectedResponse' => $invalidExpectedResponse,
      'ipnDataStore' => $ipnDataStore,
      'logger' => $logger
    ]);

    $this->assertTrue($responder->isVerified());
  }
}

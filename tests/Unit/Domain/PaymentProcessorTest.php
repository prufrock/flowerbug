<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class PaymentProcessorTest extends TestCase {

  public function testPaymentProcessorRole() {

    $paymentProcessor = new \App\Domain\PaymentProcessor();

    $this->assertTrue(method_exists($paymentProcessor, 'process'));
  }

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\PaymentProcessor::class,
      new \App\Domain\PaymentProcessor()
    );
  }

  public function testValidMessage() {

    $ipnResponder = m::mock('\Application\Domain\IpnResponder');
    $order = m::mock('\Application\Domain\Order');
    $processor = new \App\Domain\PaymentProcessor($ipnResponder, $order);

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

    $ipnResponder->shouldReceive('create')->once()->with([
      'ipnVars' => ['id' => '1'],
      'validationHeader' => $validationHeader,
      'validationCmd' => $validationCmd,
      'validationUrl' => $validationUrl,
      'validationPort' => $validationPort,
      'validationTimeout' => $validationTimeout,
      'validationExpectedResponse' => $validationExpectedResponse,
      'invalidExpectedResponse' => $invalidExpectedResponse,
      'ipnDataStore' => new \stdClass(),
      'logger' => new \stdClass()
    ]);

    $ipnResponder->shouldReceive('isVerified')->once()->andReturn(true);
    $ipnResponder->shouldReceive('isValid')->once()->andReturn(true);
    $ipnResponder->shouldReceive('hasBeenReceivedBefore')->once()->andReturn(false);
    $ipnResponder->shouldReceive('get')->withAnyArgs();
    $ipnResponder->shouldReceive('persist')->once();
    $ipnResponder->shouldReceive('getItemsPurchased')->once()->andReturn(['technique201708']);
    $ipnResponder->shouldReceive('getBuyersEmailAddress')->once()->andReturn('d.kanen+flowerbugtest@gmail.com');

    $order->shouldReceive('fulfill')->with(['technique201708'], 'd.kanen+flowerbugtest@gmail.com')->once();

    $processor->process(['id' => '1']);
  }
}

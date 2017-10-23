<?php namespace Tests\Unit\Domain;

use Tests\TestCase;

class SaleNotifierTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\SaleNotifier::class,
      new \App\Domain\SaleNotifier()
    );
  }

  public function testSuccessfulNotify() {

    $transmitter = new Transmitter();

    $this->assertTrue((new \App\Domain\SaleNotifier())->notify($transmitter));

    $expectedMessage =<<<MESSAGE
Thank you for purchase. Here are your files:<br/><br/>

February 2012 Technique Class<br/>
Microsoft Office Word<br/>
<a href="http://example.com/example.doc">example.doc</a><br/>
<br/><br/>

Adobe Acrobat PDF<br/>
<a href="http://example.com/example.pdf">example.pdf</a><br/>
<br/><br/>

Images<br/>
<a href="http://example.com/example.jpg">example.jpg</a><br/>
<br/><br/>

<br/><br/><br/>


MESSAGE;


    $this->assertEquals($expectedMessage,$transmitter->message);
  }
}

class Transmitter {

  public $message;

  public function transmit($message) {

    $this->message = $message;

    return true;
  }

  public function getProjects() {

    return [
      'February 2012 Technique Class<br/>
Microsoft Office Word<br/>
<a href="http://example.com/example.doc">example.doc</a><br/>
<br/><br/>',
      'Adobe Acrobat PDF<br/>
<a href="http://example.com/example.pdf">example.pdf</a><br/>
<br/><br/>',
      'Images<br/>
<a href="http://example.com/example.jpg">example.jpg</a><br/>
<br/><br/>'
    ];
  }
}

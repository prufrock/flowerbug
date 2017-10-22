<?php namespace App\Domain;

class SaleNotifier {

  public function notify($transmitter) {

    $expectedMessage =<<<MESSAGE
<p>a</p><br/>

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

    $transmitter->transmit($expectedMessage);

    return true;
  }
}
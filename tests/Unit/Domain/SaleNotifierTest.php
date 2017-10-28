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

    $projects = [
      new Project('February 2012 Technique Class')
    ];

    return $projects;
  }
}

class Project {

  public $title;

  public function __construct($title) {

    $this->title = $title;
    $this->guides = collect(
      [
        new Guide('example.doc', 'http://example.com/example.doc', 'doc'),
        new Guide('example.pdf', 'http://example.com/example.pdf', 'pdf'),
        new Guide('example.jpg', 'http://example.com/example.jpg', 'jpg')
      ]
    );
  }

  public function getGuides($type) {

    return $this->guides->filter(function($guide) use ($type) {
      return $guide->getFileType() == $type ? true : false;
    });
  }
}

class Guide {

  private $name;

  private $filetype;

  private $url;

  public function __construct($name, $url, $filetype) {

    $this->name = $name;
    $this->filetype = $filetype;
    $this->url = $url;
  }

  public function getUrl() {

    return $this->url;
  }

  public function getFileType() {

    return $this->filetype;
  }

  public function getName() {

    return $this->name;
  }
}

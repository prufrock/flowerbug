<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class SaleNotifierTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\SaleNotifier::class,
      new \App\Domain\SaleNotifier()
    );
  }

  public function testSuccessfulNotify() {

    $orderFulFiller = m::mock(\App\Domain\OrderFullFiller::class);
    $guideGateway = new \App\Domain\Guide();
    $project = m::mock(\App\Domain\Project::class);
    $project->shouldReceive('getTitle')->andReturn('February 2012 Technique Class\'');
    $project->shouldReceive('getGuides')->andReturn(
      collect(
        [
          $guideGateway->create(['name' => 'example.doc', 'url' => 'http://example.com/example.doc', 'file_type' => 'doc']),
          $guideGateway->create(['name' => 'example.pdf', 'url' => 'http://example.com/example.pdf', 'file_type' => 'pdf']),
          $guideGateway->create(['name' => 'example.jpg', 'url' => 'http://example.com/example.jpg', 'file_type' => 'jpg'])
        ]
      )
    );
    $orderFulFiller->shouldReceive('getProjects')->andReturn(
      collect([
        $project
      ])
    );
    $orderFulFiller->shouldReceive('transmit')->once();

    $this->assertTrue((new \App\Domain\SaleNotifier())->notify($orderFulFiller));

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

    $orderFulFiller->shouldReceive('transmit')->with($expectedMessage);
  }
}

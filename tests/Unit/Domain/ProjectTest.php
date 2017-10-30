<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class ProjectTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\Project::class,
      resolve(\App\Domain\Project::Class)
    );
  }

  public function testCreateANewProjectWithTitle() {

    $projectGateway = new \App\Domain\Project();
    $project = $projectGateway->create(['title' => 'February 2017 Technique Class']);

    $this->assertEquals('February 2017 Technique Class', $project->getTitle());
  }

  public function testFind() {

    $project = new \App\Domain\Project();
    $found = $project->find();

    $this->assertInstanceOf(\Illuminate\Support\Collection::class, $found);
    $this->assertEquals(3, $found->count());
    $this->assertInstanceOf(\App\Domain\Project::class, $found->first());
  }

  public function testGetGuides() {

    $guideGateway = m::mock(\App\Domain\Guide::class);
    $guideGateway->shouldReceive('find')->with(['technique201702'])->andReturn(collect([$guideGateway]));
    $project = (new \App\Domain\Project($guideGateway))->create(['id' => 'technique201702']);
    $guides = $project->getGuides();

    $this->assertInstanceOf(\Illuminate\Support\Collection::class, $guides);
    $this->assertEquals(1, $guides->count());
    $this->assertInstanceOf(\App\Domain\Guide::class, $guides->first());
  }
}
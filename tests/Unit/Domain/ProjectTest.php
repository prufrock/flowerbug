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

    $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
    $projectGateway = new \App\Domain\Project($simpleDbClient);
    $project = $projectGateway->create(['title' => 'February 2017 Technique Class']);

    $this->assertEquals('February 2017 Technique Class', $project->getTitle());
  }

  public function testFind() {

    $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
    $simpleDbClient->shouldReceive('select')->withAnyArgs()->with(
      [
        'SelectExpression' => 'select * from ' . config('flowerbug.projects_domain') . ' where id = \'technique201702\' or id = \'technique201703\' or id = \'technique201704\'',
        'ConsistentRead' => true
      ]
    )->once();
    $project = new \App\Domain\Project($simpleDbClient);
    $found = $project->find(['technique201702', 'technique201703', 'technique201704']);

    $this->assertInstanceOf(\Illuminate\Support\Collection::class, $found);
    $this->assertEquals(3, $found->count());
    $this->assertInstanceOf(\App\Domain\Project::class, $found->first());
  }

  public function testGetGuides() {

    $guideGateway = m::mock(\App\Domain\Guide::class);
    $guideGateway->shouldReceive('find')->with('technique201702')->andReturn(collect([$guideGateway]));
    $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
    $simpleDbClient->shouldReceive('select')->withAnyArgs()->with(
      [
        'SelectExpression' => 'select * from ' . config('flowerbug.projects_domain') . ' where id = \'technique201702\'',
        'ConsistentRead' => true
      ]
    )->once();
    $project = (new \App\Domain\Project($simpleDbClient, $guideGateway))->find('technique201702');
    $guides = $project->first()->getGuides();

    $this->assertInstanceOf(\Illuminate\Support\Collection::class, $guides);
    $this->assertEquals(1, $guides->count());
    $this->assertInstanceOf(\App\Domain\Guide::class, $guides->first());
  }
}
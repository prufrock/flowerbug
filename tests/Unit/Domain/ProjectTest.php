<?php namespace Tests\Unit\Domain;

use Tests\TestCase;

class ProjectTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\Project::class,
      resolve(\App\Domain\Project::Class)
    );
  }

  public function testFind() {

    $project = new \App\Domain\Project();
    $found = $project->find();
    $this->assertInstanceOf(\Illuminate\Support\Collection::class, $found);
    $this->assertEquals(3, $found->count());
  }
}
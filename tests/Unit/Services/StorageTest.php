<?php namespace App\Services;

use Tests\TestCase;
use \Mockery as m;

class StorageTest extends TestCase {

  private $putsObjects;

  private $object;

  public function setUp() {

    parent::setUp();

    $this->putsObjects = m::mock('PutsObjects');
    $this->object = new Storage($this->putsObjects);
  }

  public function testStore() {

    $this->putsObjects->shouldReceive('putObject')->once();
    $result = $this->object->store();

    $this->assertTrue($result, "File wasn't stored.");
  }
}

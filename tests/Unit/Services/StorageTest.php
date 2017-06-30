<?php namespace App\Services;

use Tests\TestCase;
use \Mockery as m;

class StorageTest extends TestCase {

  private $storesObjects;

  private $object;

  public function setUp() {

    parent::setUp();

    $this->storesObjects = m::mock('StoresObjects');
    $this->object = new Storage($this->storesObjects);
  }

  public function testStore() {

    $storable = m::mock('Storable');
    $this->storesObjects->shouldReceive('store')->withArgs(['storable'])->once();

    $result = $this->object->store($storable);

    $this->assertTrue($result, "File wasn't stored.");
  }
}

<?php namespace App\Services;

use Mockery\Exception\InvalidCountException;
use Tests\TestCase;

class StorageTest extends TestCase {

  private $putsObjects;

  private $object;

  public function setUp() {

    parent::setUp();

    $this->putsObjects = \Mockery::mock('PutsObjects');
    $this->object = new Storage($this->putsObjects);
  }

  public function testStore() {

    $this->putsObjects->shouldReceive('putObject')->once();
    $result = $this->object->store();

    $this->assertTrue($result, "File wasn't stored.");
  }

  protected function tearDown() {

    parent::tearDown();

    try {
      \Mockery::close();
    } catch(InvalidCountException $e) {
      $this->fail($e->getMessage());
    }
  }
}

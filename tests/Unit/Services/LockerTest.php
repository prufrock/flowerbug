<?php

use Tests\TestCase;
use \Mockery as m;
use App\Services\Locker;

class LockerTest extends TestCase {

  public function testStore() {

    $storesFiles = m::mock('FileStorer');
    $locker = new Locker($storesFiles);
    $canBeStored = m::mock('canBeStored');
    $fileDescription = [];
    $canBeStored->shouldReceive('getFileDescription')->once()->andReturn($fileDescription);
    $storesFiles->shouldReceive('putObject')->once()->withArgs([$fileDescription]);

    $this->assertTrue($locker->store($canBeStored));
  }

  public function testStoreWhenPutObjectFails() {

    $storesFiles = m::mock('FileStorer');
    $message = 'Unknown bucket';
    $storesFiles->shouldReceive('putObject')->andThrow(\Aws\S3\Exception\S3Exception::class, $message);
    $locker = new Locker($storesFiles);
    $canBeStored = m::mock('canBeStored');
    $canBeStored->shouldReceive('getFileDescription')->once()->andReturn([]);

    $this->assertFalse($locker->store($canBeStored));
    $this->assertEquals($message, $locker->getMessage());
  }
}

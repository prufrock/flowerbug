<?php

use Tests\TestCase;
use \Mockery as m;
use App\Services\Locker;

class LockerTest extends TestCase {

  public function testStore() {

    $storesFiles = m::mock('FileStorer');
    $locker = new Locker($storesFiles);
    $fileDescription = [];
    $canBeStored = m::mock('canBeStored');
    $canBeStored->shouldReceive('getFileDescription')->once()->andReturn($fileDescription);
    $storesFiles->shouldReceive('upload')->once()->withArgs([$fileDescription]);

    $this->assertTrue($locker->store($canBeStored));
  }

  public function testStoreWhenAnUploadFails() {

    $storesFiles = m::mock('FileStorer');
    $message = 'Unknown bucket';
    $storesFiles->shouldReceive('upload')->andThrow(\Aws\S3\Exception\S3Exception::class, $message);
    $locker = new Locker($storesFiles);
    $canBeStored = m::mock('canBeStored');
    $canBeStored->shouldReceive('getFileDescription')->once()->andReturn([]);

    $this->assertFalse($locker->store($canBeStored));
    $this->assertEquals($message, $locker->getMessage());
  }
}

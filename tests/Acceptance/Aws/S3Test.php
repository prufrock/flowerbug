<?php namespace Tests\Acceptance\Aws;

use Aws\S3\S3Client;
use Tests\TestCase;

class S3Test extends TestCase {

  public function testFactoryMethod() {

    $client = S3Client::factory();

    $this->assertInstanceOf(\Aws\S3\S3Client::class, $client);
  }

  public function testListBuckets() {

    $client = S3Client::factory();

    $result = $client->listBuckets();

    $this->assertGreaterThanOrEqual(1, count($result));
  }

  public function testListObjectWithoutBucket() {

    $this->expectException(\Guzzle\Service\Exception\ValidationException::class);
    $this->expectExceptionMessage('Validation errors: [Bucket] is a required string');

    $client = S3Client::factory();

    $result = $client->getIterator('ListObjects', ['Bucket' => null]);

    foreach($result as $object) {
      return false;
    }

  }

  public function testListObjectsWithIterator() {

    $client = S3Client::factory();

    $result = $client->getIterator('ListObjects', ['Bucket' => config('flowerbug.s3.projects_bucket')]);

    foreach($result as $object) {
      $this->assertEquals(['Key', 'LastModified', 'ETag', 'Size', 'Owner', 'StorageClass'], array_keys($object));
    }
  }

  public function testListObjectsInAFolderWithIterator() {

    $client = S3Client::factory();

    $iterator = $client->getIterator('ListObjects', [
      'Bucket' => config('flowerbug.s3.projects_bucket'),
      'Prefix' => env('FLOWERBUG_S3_TEST_PREFIX')
    ]);

    foreach ($iterator as $object) {
      $this->assertEquals(['Key', 'LastModified', 'ETag', 'Size', 'Owner', 'StorageClass'], array_keys($object));
    }

    $this->assertGreaterThanOrEqual(1, $iterator->count());
  }

  public function testListObjectIteratorIsEmptyUntilRun() {

    $client = S3Client::factory();

    $iterator = $client->getIterator('ListObjects', [
      'Bucket' => config('flowerbug.s3.projects_bucket'),
      'Prefix' => env('FLOWERBUG_S3_TEST_PREFIX')
    ]);

    $this->assertEquals(0, $iterator->count());

    foreach ($iterator as $object) { continue;}

    $this->assertGreaterThanOrEqual(1, $iterator->count());
  }
}
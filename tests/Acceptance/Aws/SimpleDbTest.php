<?php namespace Tests\Acceptance\Aws;

use Aws\SimpleDb\SimpleDbClient;
use Tests\TestCase;

class SimpleDbTest extends TestCase {

  public function testCreateClient() {

    $client = SimpleDbClient::factory(['region' => 'us-east-1']);;

    $this->assertInstanceOf(\Aws\SimpleDb\SimpleDbClient::class, $client);
  }

  public function testClientIsAbleToMakeARequest() {

    $client = SimpleDbClient::factory(['region' => 'us-east-1']);;

    $result = $client->getIterator('ListDomains')->toArray();
    $this->assertGreaterThan(0, count($result));
  }
}

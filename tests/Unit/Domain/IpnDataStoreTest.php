<?php namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class IpnDataStoreTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(
      \App\Domain\IpnDataStore::class,
      new \App\Domain\IpnDataStore
    );
  }

  public function testThatAMessageDoesExist() {

    $sdb = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
    $sdb->shouldReceive('getAttributes')->with(
      [
        'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
        'ItemName' => '0XV34832YX668463W',
        'ConsistentRead' => true
      ]
    )->once();

    $this->assertTrue((new \App\Domain\IpnDataStore($sdb))->doesMessageExist());
  }
}

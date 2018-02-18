<?php 

namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class IpnDataStoreTest extends TestCase
{
    public function testNew()
    {
        $this->assertInstanceOf(\App\Domain\IpnDataStore::class, app(\App\Domain\IpnDataStore::class));
    }

    public function testThatAMessageDoesExist()
    {
        $sdb = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $sdb->shouldReceive('getAttributes')->with([
                'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
                'ItemName' => '0XV34832YX668463W',
                'ConsistentRead' => true,
            ])->andReturn(['Attributes' => ''])->once();

        $message['txn_id'] = '0XV34832YX668463W';

        $this->assertTrue((new \App\Domain\IpnDataStore($sdb))->doesMessageExist($message));
    }

    public function testThatAMessageDoesNotExist()
    {
        $sdb = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $sdb->shouldReceive('getAttributes')->with([
                'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
                'ItemName' => '0XV34832YX668463W',
                'ConsistentRead' => true,
            ])->andReturn([])->once();

        $message['txn_id'] = '0XV34832YX668463W';

        $this->assertFalse((new \App\Domain\IpnDataStore($sdb))->doesMessageExist($message));
    }

    public function testSuccessfullyStoringAMessage()
    {
        $sdb = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $sdb->shouldReceive('putAttributes')->with([
                'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
                'ItemName' => '0XV34832YX668463W',
                'Attributes' => [['Name' => 'txn_id', 'Value' => '0XV34832YX668463W']],
            ])->andReturn(['Attributes' => ''])->once();

        $this->assertTrue((new \App\Domain\IpnDataStore($sdb))->storeMessage(['txn_id' => '0XV34832YX668463W']));
    }

    public function testFailingToStoreAMessage()
    {
        $sdb = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $sdb->shouldReceive('putAttributes')->with([
                'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
                'ItemName' => '0XV34832YX668463W',
                'Attributes' => [['Name' => 'txn_id', 'Value' => '0XV34832YX668463W']],
            ])->andReturn([])->once();

        $this->assertFalse((new \App\Domain\IpnDataStore($sdb))->storeMessage(['txn_id' => '0XV34832YX668463W']));
    }
}

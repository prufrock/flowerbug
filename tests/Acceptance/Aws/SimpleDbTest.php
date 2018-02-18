<?php

namespace Tests\Acceptance\Aws;

use Aws\SimpleDb\SimpleDbClient;
use Tests\TestCase;

class SimpleDbTest extends TestCase
{
    private $itemsToDelete = [];

    public function testCreateClient()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $this->assertInstanceOf(\Aws\SimpleDb\SimpleDbClient::class, $client);
    }

    public function testClientIsAbleToMakeARequest()
    {

        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $result = $client->getIterator('ListDomains')->toArray();
        $this->assertGreaterThanOrEqual(0, count($result));
    }

    public function testCreateDomainPermissionFailure()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $this->expectException(\Aws\SimpleDb\Exception\SimpleDbException::class);
        $this->expectExceptionMessage('does not have permission to perform (sdb:CreateDomain) on resource');

        $client->createDomain(['DomainName' => 'TestDomain']);
    }

    public function testCreateItem()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $result = $client->getAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'test',
                'Attributes' => ['a'],
                'ConsistentRead' => true,
            ]);

        $this->assertEquals(0, count($result['Items']));

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'test',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'alpha'],
                ],
            ]);

        $this->itemsToDelete[] = 'test';

        $result = $client->getAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'test',
                'Attributes' => ['a'],
                'ConsistentRead' => true,
            ]);

        $this->assertEquals('alpha', $result['Attributes'][0]['Value']);
    }

    public function testSelectOneItem()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'test',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'alpha'],
                ],
            ]);

        $this->itemsToDelete[] = 'test';

        $result = $client->select([
                'SelectExpression' => 'select * from '.env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN').' where a = \'alpha\'',
                'ConsistentRead' => true,
            ]);

        $this->assertEquals(1, count($result['Items']));
    }

    public function testSelectMoreThanOneItem()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'testOne',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'alpha'],
                ],
            ]);

        $this->itemsToDelete[] = 'testOne';

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'testTwo',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'alpha'],
                ],
            ]);

        $this->itemsToDelete[] = 'testTwo';

        $result = $client->select([
                'SelectExpression' => 'select * from '.env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN').' where a = \'alpha\'',
                'ConsistentRead' => true,
            ]);

        $this->assertEquals(2, count($result['Items']));
    }

    public function testSelectTwoItemsWithAnOr()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'testZebra',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'zebra'],
                ],
            ]);

        $this->itemsToDelete[] = 'testZebra';

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'testTango',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'tango'],
                ],
            ]);

        $this->itemsToDelete[] = 'testTango';

        $result = $client->select([
                'SelectExpression' => 'select * from '.env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN').' where a = \'zebra\' OR a = \'tango\'',
                'ConsistentRead' => true,
            ]);

        $this->assertEquals(2, count($result['Items']));
    }

    public function testGetItemNameWithArrayDoesntWork()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'testZebra',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'zebra'],
                ],
            ]);

        $this->itemsToDelete[] = 'testZebra';

        $client->putAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => 'testTango',
                'Attributes' => [
                    ['Name' => 'a', 'Value' => 'tango'],
                ],
            ]);

        $this->itemsToDelete[] = 'testTango';

        $this->expectException(\Guzzle\Service\Exception\ValidationException::class);
        $this->expectExceptionMessage('[ItemName] must be of type string');

        $client->getAttributes([
                'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                'ItemName' => $this->itemsToDelete,
                'Attributes' => ['a'],
                'ConsistentRead' => true,
            ]);
    }

    public function tearDown()
    {
        $client = SimpleDbClient::factory(['region' => env('AWS_REGION')]);

        foreach ($this->itemsToDelete as $itemName) {
            $client->deleteAttributes([
                    'DomainName' => env('FLOWERBUG_SIMPLEDB_TEST_DOMAIN'),
                    'ItemName' => $itemName,
                ]);
        }
    }
}

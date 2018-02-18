<?php 

namespace App\Domain;

class IpnDataStore
{
    private $sdb;

    public function __construct(\Aws\SimpleDb\SimpleDbClient $sdb)
    {
        $this->sdb = $sdb;
    }

    public function doesMessageExist($message)
    {
        $result = $this->sdb->getAttributes([
                'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
                'ItemName' => $message['txn_id'],
                'ConsistentRead' => true,
            ]);

        return isset($result['Attributes']);
    }

    public function storeMessage($message)
    {
        $attributes = [];

        foreach ($message as $k => $v) {
            $attribute = [
                'Name' => $k,
                'Value' => $v,
            ];

            $attributes[] = $attribute;
        }

        $result = $this->sdb->putAttributes([
                'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
                'ItemName' => $message['txn_id'],
                'Attributes' => $attributes,
            ]);

        return isset($result['Attributes']);
    }
}
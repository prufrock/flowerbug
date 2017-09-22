<?php namespace App\Domain;

class IpnDataStore {

  private $sdb;

  public function __construct($sdb = null) {

    $this->sdb = $sdb;
  }

  public function doesMessageExist($message) {

    $result = $this->sdb->getAttributes(
      [
        'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
        'ItemName' => $message['txn_id'],
        'ConsistentRead' => true
      ]
    );

    return isset($result['Attributes']);
  }

  public function storeMessage($message) {

    $result = $this->sdb->putAttributes(
      [
        'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
        'ItemName' => $message['txn_id'],
        'Attributes' => $message
      ]
    );

    return isset($result['Attributes']);
  }
}
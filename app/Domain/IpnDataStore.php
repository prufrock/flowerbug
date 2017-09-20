<?php namespace App\Domain;

class IpnDataStore {

  private $sdb;

  public function __construct($sdb = null) {

    $this->sdb = $sdb;
  }

  public function doesMessageExist() {

    $this->sdb->getAttributes(
      [
        'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
        'ItemName' => '0XV34832YX668463W',
        'ConsistentRead' => true
      ]
    );
    return true;
  }
}
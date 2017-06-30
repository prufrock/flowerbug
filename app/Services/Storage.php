<?php namespace App\Services;

class Storage {

  private $storesObjects;

  public function __construct($storesObjects) {
    $this->storesObjects = $storesObjects;
  }

  public function store($storable) {

    $this->storesObjects->store($storable);
    return true;
  }
}
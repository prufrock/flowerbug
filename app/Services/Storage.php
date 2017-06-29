<?php namespace App\Services;

class Storage {

  private $putsObjects;

  public function __construct($putsObjects) {
    $this->putsObjects = $putsObjects;
  }

  public function store() {

    $this->putsObjects->putObject();
    return true;
  }
}
<?php namespace App\Services;

use Aws\S3\Exception\S3Exception;

class Locker {

  private $storesObjects;

  private $message;

  public function __construct($storesObjects) {
    $this->storesObjects = $storesObjects;
  }

  public function store($canBeStored) {

    try {
      $this->storesObjects->putObject($canBeStored->getFileDescription());
    } catch (S3Exception $e) {
      $this->message = $e->getMessage();
      return false;
    }

    return true;
  }

  public function getMessage() {
    return $this->message;
  }
}
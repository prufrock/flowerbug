<?php namespace App\Domain;

class Project {

  public function find() {

    return collect([new self, new self, new self]);
  }
}
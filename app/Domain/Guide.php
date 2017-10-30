<?php namespace App\Domain;

class Guide {

  private $attributes;

  public function create($attributes = []) {

    $guide = new self;
    $guide->setAttributes($attributes);
    return $guide;
  }

  public function getName() {

    return $this->attributes['name'];
  }

  public function getUrl() {

    return $this->attributes['url'];
  }

  public function getFileType() {

    return $this->attributes['file_type'];
  }

  public function find() {

    return collect();
  }

  private function setAttributes($attributes) {

    $this->attributes = $attributes;
  }
}

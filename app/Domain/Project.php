<?php namespace App\Domain;

class Project {

  private $attributes;

  private $guideGateway;

  public function __construct($guideGateway = null) {

    $this->guideGateway = $guideGateway;
  }

  public function create($attributes = []) {

    $project = new self($this->guideGateway);
    $project->setAttributes($attributes);

    return $project;
  }

  public function getTitle() {

    return $this->attributes['title'];
  }

  public function getId() {

    return $this->attributes['id'];
  }

  public function find() {

    return collect([$this->create(), $this->create(), $this->create()]);
  }

  public function getGuides() {

    return $this->guideGateway->find([$this->getId()]);
  }

  private function setAttributes($attributes) {

    $this->attributes = $attributes;
  }
}
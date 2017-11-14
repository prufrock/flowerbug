<?php namespace App\Domain;

use Aws\SimpleDb\SimpleDbClient;

class Project {

  private $attributes;

  private $guideGateway;

  private $simpleDb;

  public function __construct(SimpleDbClient $simpleDb, $guideGateway = null) {

    $this->simpleDb = $simpleDb;
    $this->guideGateway = $guideGateway;
  }

  public function create($attributes = []) {

    $project = new self($this->simpleDb, $this->guideGateway);
    $project->setAttributes($attributes);

    return $project;
  }

  public function getTitle() {

    return $this->attributes['title'];
  }

  public function getId() {

    return $this->attributes['id'];
  }

  public function find($ids = null) {

    $predicateClauses = collect($ids)->map(function($id) {
      return "id = '$id'";
    }
    );

    $predicate = $predicateClauses->implode(' or ');

    $this->simpleDb->select(
      [
        'SelectExpression' => 'select * from ' . config('flowerbug.projects_domain') . ' where ' . $predicate,
        'ConsistentRead' => true
      ]
    );

    return collect(collect($ids)->map(function($id) {
      return $this->create(['id' => $id]);
    }
    ));
  }

  public function getGuides() {

    return $this->guideGateway->find($this->getId());
  }

  private function setAttributes($attributes) {

    $this->attributes = $attributes;
  }
}
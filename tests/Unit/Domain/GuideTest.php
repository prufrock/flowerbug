<?php namespace Tests\Unit\Domain;

use Tests\TestCase;

class GuideTest extends TestCase {

  public function testNew() {

    $this->assertInstanceOf(\App\Domain\Guide::class, resolve(\App\Domain\Guide::class));
  }

  public function testCreateWithNameAndUrlAndFileType() {

    $guideGateway = new \App\Domain\Guide;
    $guide = $guideGateway->create(
      [
        'name' => 'example.doc',
        'url' => 'http://example.com/example.doc',
        'file_type' => 'doc'
      ]
    );

    $this->assertInstanceOf(\App\Domain\Guide::class, $guide);
    $this->assertEquals('example.doc', $guide->getName());
    $this->assertEquals('http://example.com/example.doc', $guide->getUrl());
    $this->assertEquals('doc', $guide->getFileType());
  }

  public function testFind() {

    $guide = new \App\Domain\Guide;

    $this->assertInstanceOf(\Illuminate\Support\Collection::class, $guide->find());
  }
}

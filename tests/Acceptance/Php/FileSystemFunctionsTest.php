<?php namespace Tests\Acceptance\Php;

use Tests\TestCase;

class FileSystemFunctionsTest extends TestCase {

  public function test_fgets() {

    $temp = tmpfile();
    fwrite($temp, 'writing to tempfile');
    fseek($temp, 0);
    $this->assertEquals('writing to tempfile', fgets($temp, 1024));
  }

  public function test_fopen_with_url() {

    $temp = fopen("http://flowerbug.app", 'r');
    
    stream_set_timeout($temp, 10);

    $this->assertNotEmpty(fgets($temp));
  }

  public function test_file_get_contents_from_url() {

    $this->assertNotEmpty(file_get_contents('http://flowerbug.app'));
  }

  public function tearDown() {

    parent::tearDown();
  }
}
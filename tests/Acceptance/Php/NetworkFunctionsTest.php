<?php namespace Tests\Acceptance\Php;

use Tests\TestCase;

class NetworkFunctionsTest extends TestCase {

  public function test_fsockopen_with_url() {
    
    $header = "GET / HTTP/1.1\r\n";
    $header .= "Host: flowerbug.app\r\n";
    $header .= "Connection: close\r\n\r\n";
    $temp = fsockopen("flowerbug.app", 80, $errno, $errstr, 3);
    stream_set_timeout($temp, 3);
    
    fputs($temp, $header);
    
    $this->assertFalse(feof($temp));
    $data = fread($temp, 1024);
    $this->assertNotEmpty($data);
    $this->assertEquals(1024, strlen($data));
  }

  public function test_fsockopen_with_ip() {

    $header = "GET / HTTP/1.1\r\n";
    $header .= "Host: 127.0.0.1\r\n";
    $header .= "Connection: close\r\n\r\n";
    $temp = fsockopen("127.0.0.1", 80, $errno, $errstr, 3);
    stream_set_timeout($temp, 3);
    
    fputs($temp, $header);
    
    $this->assertFalse(feof($temp));
    $this->assertNotEmpty(fread($temp, 1024));
  }
}

<?php namespace Tests\Acceptance\Php;

use Tests\TestCase;

class StreamFunctionsTest extends TestCase {

  public function test_stream_get_meta_data() {

    $errNo = 0;
    $errStr = '';

    $temp = fsockopen("127.0.0.1", 80, $errNo, $errStr, 3.0);
    stream_set_timeout($temp, 3);
    $info = stream_get_meta_data($temp);
    
    $this->assertSame(false, $info['timed_out']);
    $this->assertSame(true, $info['blocked']);
    $this->assertSame(false, $info['eof']);
    $this->assertEquals('tcp_socket/ssl', $info['stream_type']);
    $this->assertEquals('r+', $info['mode']);
    $this->assertSame(0, $info['unread_bytes']);
    $this->assertSame(false, $info['seekable']);
  }
}
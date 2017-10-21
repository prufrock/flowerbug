<?php namespace App\Domain;

class FilePointerProxy {

   public function fsockopen($hostname, $port, $errno = null, $errstr = null, $timeout = null) {

    return fsockopen($hostname, $port, $errno, $errstr, $timeout);
  }

  public function fputs($handle, $string, $length = null) {

    return fputs($handle, $string, $length);
  }

  public function feof($handle) {

     return feof($handle);
  }

  public function fgets($handle, $length = null) {

     return fgets($handle, $length);
  }

  public function fclose($handle) {

     return fclose($handle);
  }
}

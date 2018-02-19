<?php 

namespace App\Domain;

class FilePointerProxy
{
    public function fsockopen($hostname, $port, $errno = null, $errstr = null, $timeout = null)
    {
        return fsockopen($hostname, $port, $errno, $errstr, $timeout);
    }

    public function fputs($handle, $string, $length = null)
    {
        //watch out if you pass null as the third argument fputs won't write data. 
        if ($length) {
            return fputs($handle, $string, $length);
        } else {
            return fputs($handle, $string);
        }
    }

    public function feof($handle)
    {
        return feof($handle);
    }

    public function fgets($handle, $length = null)
    {
        return fgets($handle, $length);
    }

    public function fclose($handle)
    {
        return fclose($handle);
    }
}

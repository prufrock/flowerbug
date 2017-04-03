<?php namespace App\Services;

/**
 * Class S3UploadService upload a file to S3.
 * @package App\Services\S3UploadService
 */
class S3UploadService {

  /**
   * Uploads a file to S3.
   * @param $pathToFile
   * @param $bucket
   * @param $pathToDestination
   * @param $compress
   * @param $makePublic
   * @return bool
   */
  public function upload($pathToFile, $bucket, $pathToDestination, $compress, $makePublic) {

    print "uploaded" . PHP_EOL;
    return true;
  }
}
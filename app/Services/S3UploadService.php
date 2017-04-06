<?php namespace App\Services;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

/**
 * Class S3UploadService upload a file to S3.
 * @package App\Services\S3UploadService
 */
class S3UploadService {

  /**
   * Set if an error occurs.
   * @var string|null
   */
  private $errorMessage = null;

  /**
   * Set if an error occurs.
   * @return null|string
   */
  public function getErrorMessage() {
    
    return $this->errorMessage;
  }

  /**
   * Uploads a file to S3.
   * @param $pathToFile
   * @param $bucket
   * @param $pathToDestination
   * @param $contentType
   * @param $compress
   * @param $makePublic
   * @return bool
   */
  public function upload($pathToFile, $bucket, $pathToDestination, $contentType, $compress, $makePublic) {

    $succeeded = false;
    $client = null;
    $args = null;
    $this->errorMessage = null;

    $client = S3Client::factory();

    $args = [
      'Bucket' => $bucket,
      'Key' => $pathToDestination,
      'SourceFile' => $pathToFile,
      'ContentType' => $contentType
    ];

    if ($compress) {

      //TODO compress the file
      $args['ContentEncoding'] = 'gzip';
    }

    if ($makePublic) {

      // http://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.S3.S3Client.html#_putObjectAcl
      $args['ACL'] = 'public-read';
    } else {

      $args['ACL'] = 'private';
    }

    try {

      // http://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.S3.S3Client.html#_putObject
      $succeeded = $client->putObject($args);
    } catch (S3Exception $e) {

      $this->errorMessage = $e->getMessage();
    }

    return $succeeded;
  }
}
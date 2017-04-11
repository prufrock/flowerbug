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
   * Uploads a stream to S3.
   * @param resource $stream The stream to upload.
   * @param string $bucket The bucket to upload to.
   * @param string $pathToDestination The path to S3 folder it should be uploaded to.
   * @param string $contentType The content type header to set.
   * @param bool $compressed Whether or not the file is compresssed
   * @param bool $makePublic Whether or not the file should be publicly accessible.
   * @return bool
   */
  public function uploadStream($stream, $bucket, $pathToDestination, $contentType, $compressed, $makePublic) {

    $succeeded = false;
    $client = null;
    $args = null;
    $this->errorMessage = null;

    $client = S3Client::factory();

    $args = [
      'Bucket' => $bucket,
      'Key' => $pathToDestination,
      'Body' => $stream,
      'ContentType' => $contentType
    ];

    if ($compressed) {

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

    $client->waitUntil('ObjectExists', array(
      'Bucket' => $bucket,
      'Key'    => $pathToDestination
    ));

    return $succeeded;
  }
}
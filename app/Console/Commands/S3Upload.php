<?php

namespace App\Console\Commands;

use App\Services\Locker;
use Illuminate\Console\Command;
use App\Utilities\Constants;

/**
 * Class S3Upload provides command line access to the S3Upload service.
 * @package App\Console\Commands
 */
class S3Upload extends Command {

  /**
   * Upload successful.
   */
  const EXIT_SUCCESS = 0;

  /**
   * Upload failed.
   */
  const EXIT_FAILURE = 1;

  private $fileDescription = [];

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 's3:upload ' .
  '{bucket : The name of the bucket to upload to.} ' .
  '{path-to-destination : The name of the file to upload to.} ' .
  '{content-type : The content type of the file.} ' .
  '{--z|gzip : compress the file, upload it and set content encoding.} ' .
  '{--p|public : make the file public.}' .
  '{--d|dry-run : Shows what would have been uploaded.}' .
  '{--debug : Show debug output.}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Uploads a file to S3.';

  /**
   * @var Locker
   */
  private $locker;

  /**
   * @param array $fileDescription
   */
  private function setFileDescription(array $fileDescription) {
    $this->fileDescription = $fileDescription;
  }

  /**
   * Create a new command instance.
   *
   * @param Locker $locker Knows how to upload files to S3.
   */
  public function __construct(Locker $locker) {

    parent::__construct();
    $this->locker = $locker;
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {

    $aDryRun = $this->option('dry-run');
    $stdin = fopen('php://stdin', 'r');
    $bucket = $this->argument('bucket');
    $pathToDestination = $this->argument('path-to-destination');
    $contentType = $this->argument('content-type');
    $contentEncoding = $this->option('gzip') ? 'gzip' : '';
    $acl = $this->option('public') ? 'public-read' : 'private';
    $debug = $this->option('debug');
    $succeeded = false;

    $fileDescription = [
      'Bucket' => $bucket,
      'Key' => $pathToDestination,
      'Body' => $stdin,
      'ContentType' => $contentType,
      'ContentEncoding' => $contentEncoding,
      'ACL' => $acl
    ];

    $this->setFileDescription($fileDescription);

    if (!$aDryRun) {

      $succeeded = $this->locker->store($this);
    }

    if ($debug) {

      if ($succeeded) {

        $this->info('Uploaded to ' . $bucket . ' ' . $pathToDestination);
      } else {

        $this->info($this->locker->getErrorMessage());
      }
    }

    if ($succeeded) {

      return self::EXIT_SUCCESS;
    } else {

      return self::EXIT_FAILURE;
    }
  }

  /**
   * @return array
   */
  public function getFileDescription(): array {
    return $this->fileDescription;
  }
}

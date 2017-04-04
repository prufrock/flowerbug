<?php

namespace App\Console\Commands;

use App\Services\S3UploadService;
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

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 's3:upload ' .
    '{path-to-file : The file to upload.} ' .
    '{bucket : The name of the bucket to upload to.} ' .
    '{path-to-destination : The name of the file to upload to.} ' .
    '{content-type : The content type of the file.} ' .
    '{--z|gzip : compress the file, upload it and set content encoding.} ' .
    '{--p|public : make the file public.}'.
    '{--d|dry-run : Shows what would have been uploaded.}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Uploads a file to S3.';

  /**
   * @var S3UploadService
   */
  private $service;

  /**
   * Create a new command instance.
   *
   * @param S3UploadService $service Knows how to upload files to S3.
   */
  public function __construct(S3UploadService $service) {

    parent::__construct();
    $this->service = $service;
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {

    $aDryRun = $this->option('dry-run');
    $pathToFile = $this->argument('path-to-file');
    $bucket = $this->argument('bucket');
    $pathToDestination = $this->argument('path-to-destination');
    $contentType = $this->argument('content-type');
    $compress = $this->option('gzip');
    $makePublic = $this->option('public');
    $verbose = $this->option('verbose');
    $succeeded = false;


    if (!$aDryRun) {

      $succeeded = $this->service->upload($pathToFile, $bucket, $pathToDestination, $contentType, $compress, $makePublic);
    }

    if ($verbose) {
      if ($succeeded) {

        $this->info($pathToFile . ' ' . Constants::COPY_TO_SIGIL . ' ' . $bucket . ' ' . $pathToDestination);
      } else {

        $this->info($this->service->getErrorMessage());
      }
    }

    if ($succeeded) {
      return self::EXIT_SUCCESS;
    } else {
      return self::EXIT_FAILURE;
    }
  }
}

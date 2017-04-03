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
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 's3:upload ' .
    '{path-to-file : The file to upload.} ' .
    '{bucket : The name of the bucket to upload to.} ' .
    '{path-to-destination : The name of the file to upload to.} ' .
    '{--z|gzip : compress the file, upload it and set content encoding.} ' .
    '{--p|public make the file public.}'.
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

    $aDryRun = false;
    $pathToFile = '';
    $bucket = '';
    $pathToDestination = '';
    $compress = false;
    $makePublic = false;
    $succeeded = false;


    if (!$aDryRun) {
      $succeeded = $this->service->upload($pathToFile, $bucket, $pathToDestination, $compress, $makePublic);
    }

    if ($succeeded) {
      $this->info($pathToFile . ' ' . Constants::COPY_TO_SIGIL . ' ' . $bucket . ' ' . $pathToDestination);
    }
  }
}

<?php

namespace App\Console\Commands;

use Aws\SimpleDb\SimpleDbClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class AwsSimpleDbFlowerbugProjectsPut extends Command {

  const COPY_TO_SIGIL = '>';

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'projects:put {path-to-project-folder : The path to the project folder.} {--d|dry-run : Shows what would have been put.}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Add or modify a project in the flowerbug database.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {

    $aDryRun =  null;
    $projectId = null;

    // Set options
    $aDryRun = $this->option('dry-run');
    $path = $this->argument('path-to-project-folder') . '/project.json';

    $client = SimpleDbClient::factory(
      array(
        'region' => 'us-east-1'
      )
    );

    $domainName = Config::get('flowerbug.simpledb.projects_domain');

    $kvp = json_decode(file_get_contents($path), true);

    $projectId = $kvp['id'];
    $attributes = [];

    foreach($kvp as $k => $v) {
      $attributes[] = ['Name' => $k, 'Value' => $v, 'Replace' => true];
    }

    // http://docs.aws.amazon.com/AmazonSimpleDB/latest/DeveloperGuide/WorkingWithDataPut.html
    if (!$aDryRun) {
      $client->putAttributes(
        [
          'DomainName' => $domainName,
          'ItemName' => $projectId,
          'Attributes' => $attributes
        ]
      );
    }
    $this->info($path . ' ' . self::COPY_TO_SIGIL .' ' . DIRECTORY_SEPARATOR . $domainName . DIRECTORY_SEPARATOR . $projectId);
  }
}

<?php

namespace App\Console\Commands;

use Aws\SimpleDb\SimpleDbClient;
use Illuminate\Console\Command;

class AwsSimpleDbListDomainContents extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'SimpleDb:domain {domain : The domain to list.}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'List the contents of a domain.';

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

    // Set options
    $domain = $this->argument('domain');

    $client = SimpleDbClient::factory(
      array(
        'region' => 'us-east-1'
      )
    );

    $iterator = $client->getIterator('Select', array(
      'SelectExpression' => "select * from " . $domain
    ));

    $perPage = 25;
    $counter = 0;
    $rows = [];
    foreach ($iterator as $item) {
      dd($item);
      $rows[$counter % $perPage] = [$item['Name']];
      $counter++;
      if ($counter % $perPage == 0) {
        $this->table(['name'], $rows);
        if(!$this->confirm('continue?')) {
          break;
        }
      }
    }
  }
}

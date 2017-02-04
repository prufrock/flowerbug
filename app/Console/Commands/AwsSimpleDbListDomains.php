<?php

namespace App\Console\Commands;

use Aws\SimpleDb\SimpleDbClient;
use Illuminate\Console\Command;

class AwsSimpleDbListDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SimpleDb:list-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $client = SimpleDbClient::factory(array(
        'region'  => 'us-east-1'
      ));

      $domains = $client->getIterator('ListDomains')->toArray();

      foreach($domains as $domain) {
        $this->line($domain);
      }
    }
}

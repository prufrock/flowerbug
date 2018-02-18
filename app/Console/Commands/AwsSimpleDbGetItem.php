<?php

namespace App\Console\Commands;

use Aws\SimpleDb\SimpleDbClient;
use Illuminate\Console\Command;

class AwsSimpleDbGetItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SimpleDb:get-item {domain : The domain to list.} {ItemName : The name of the item to get.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves an item from SimpleDb.';

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
        $domain = $this->argument('domain');
        $itemName = $this->argument('ItemName');

        $client = SimpleDbClient::factory([
                'region' => 'us-east-1',
            ]);

        dd($client->getAttributes([
                'DomainName' => $domain,
                'ItemName' => $itemName,
                'ConsistentRead' => true,
            ]));
    }
}

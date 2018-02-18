<?php

namespace App\Console\Commands;

use Aws\SimpleDb\SimpleDbClient;
use Illuminate\Console\Command;

class TestSimpleDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws:sdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to play around with AWS SDB.';

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
        // http://docs.aws.amazon.com/aws-sdk-php/v2/guide/
        // http://docs.aws.amazon.com/aws-sdk-php/v2/guide/service-simpledb.html

        $awsAccessKeyID = env('AWS_ACCESS_KEY_ID');
        $awsSecretAccessKey = env('AWS_SECRET_ACCESS_KEY');

        $client = SimpleDbClient::factory([
            'region' => 'us-east-1',
        ]);

        $domains = $client->getIterator('ListDomains')->toArray();

        var_dump($domains);
    }
}

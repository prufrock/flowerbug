<?php namespace Tests\Acceptance\Aws;

use Aws\Ses\SesClient;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SesTest extends TestCase {

  public function testFactoryMethod() {

    $client = SesClient::factory(['region' => Config::get('flowerbug.aws_region')]);

    $this->assertInstanceOf(\Aws\Ses\SesClient::class, $client);
  }
  
  public function testSendEmail() {
    
    $client = SesClient::factory(['region' => Config::get('flowerbug.aws_region')]);
    
    $result = $client->sendEmail([
      'Source' => Config::get('flowerbug.seller_address'),
      'Destination' => [
        'ToAddresses' => [Config::get('flowerbug.seller_address')]
      ],
      'Message' => [
        'Subject' => ['Data' => 'testSendEmail', 'Charset' => 'UTF-8'],
        'Body' => ['Html' => ['Data' => '<strong>Hi!</strong><br/>This is from your test!<br/>Sincerly,<br/>A. Test', 'Charset' => 'UTF-8']],
      ],
      'ReplyToAddresses' => [Config::get('flowerbug.seller_address')]
    ]);
    
    $this->assertObjectHasAttribute('MessageId', $result);
  }
}


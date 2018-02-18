<?php 

namespace Tests\Unit\Domain;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Mockery as m;

class TransmitterTest extends TestCase
{
    public function testNew()
    {
        $this->assertInstanceOf(\App\Domain\Transmitter::class, resolve(\App\Domain\Transmitter::class));
    }

    public function testTransmit()
    {
        config::shouldReceive('get')->with('flowerbug.seller_address')->andReturn('seller@example.com')->once();
        config::shouldReceive('get')->with('flowerbug.email_subject')->andReturn('congrats on your purchase')->once();
        $ses = m::mock(\Aws\Ses\SesClient::class);
        $transmitter = new \App\Domain\Transmitter($ses);
        $ses->shouldReceive('sendEmail')->with([
            'Source' => 'seller@example.com',
            'Destination' => [
                'ToAddresses' => ['buyer@example.com', 'seller@example.com'],
            ],
            'Message' => [
                'Subject' => [
                    'Data' => 'congrats on your purchase',
                    'Charset' => 'UTF-8',
                ],
                'Body' => [
                    'Html' => [
                        'Data' => 'You bought some stuff!',
                        'Charset' => 'UTF-8',
                    ],
                ],
                'ReplyToAddresses' => ['seller@example.com'],
            ],
        ])->once();

        $this->assertTrue($transmitter->transmit('buyer@example.com', 'You bought some stuff!'));
    }
}

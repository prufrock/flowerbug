<?php 

namespace Tests\Unit\Domain;

use App\Domain\IpnMessage;
use Tests\TestCase;
use Mockery as m;

class IpnMessageTest extends TestCase
{
    public function testNew()
    {
        $this->assertInstanceOf(IpnMessage::class, new IpnMessage(m::mock(\App\Domain\IpnResponder::class)));
    }

    public function testGetASingleKey()
    {
        $ipnMessage = new IpnMessage(m::mock(\App\Domain\IpnResponder::class));
        $ipnMessage->data = ['txn_id' => 1];

        $this->assertEquals(1, $ipnMessage->data['txn_id']);
    }

    public function testGetTheWholeMessageAsAnArray()
    {
        $ipnMessage = new IpnMessage(m::mock(\App\Domain\IpnResponder::class));
        $ipnMessage->data = ['txn_id' => 1];

        $this->assertEquals(['txn_id' => 1], $ipnMessage->data);
    }

    public function testGetBuyersEmailAddress()
    {
        $ipnMessage = new IpnMessage(m::mock(\App\Domain\IpnResponder::class));
        $ipnMessage->data = ['payer_email' => 'buyer@example.com'];

        $this->assertEquals('buyer@example.com', $ipnMessage->getBuyersEmailAddress());
    }

    public function testGetEmptyBuyersEmailAddress()
    {
        $ipnMessage = new IpnMessage(m::mock(\App\Domain\IpnResponder::class));
        $ipnMessage->data = [];

        $this->assertEquals('', $ipnMessage->getBuyersEmailAddress());
    }

    public function testGetItemsPurchasedReturnsItems()
    {
        $responder = m::mock(\App\Domain\IpnResponder::class);
        $responder->shouldReceive('getItemsPurchased')->andReturn(['technique201707', 'technique201708']);

        $ipnMessage = new IpnMessage($responder);
        $ipnMessage->data = [
            'item_number_1' => 'technique201707',
            'item_number_2' => 'technique201708',
        ];

        $this->assertEquals(['technique201707', 'technique201708'], $ipnMessage->getItemsPurchased());
    }

    public function testGetTxnId()
    {
        $ipnMessage = new IpnMessage(m::mock(\App\Domain\IpnResponder::class));
        $ipnMessage->data = ['txn_id' => 1];

        $this->assertEquals(1, $ipnMessage->get('txn_id'));
    }
}

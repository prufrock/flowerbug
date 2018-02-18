<?php

namespace Tests\Feature;

use Aws\SimpleDb\SimpleDbClient;
use Tests\TestCase;
use Mockery as m;

class IpnTest extends TestCase
{
    public function testReceiveValidIpnMessage()
    {
        $data = [
            "payment_type" => "instant",
            "payment_date" => "Wed Nov 22 2017 21:29:19 GMT-0600 (CST)",
            "payment_status" => "Completed",
            "payer_status" => "verified",
            "first_name" => "Test",
            "last_name" => "Testerson",
            "payer_email" => config('flowerbug.test.payer_email'),
            "payer_id" => "TESTBUYERID01",
            "address_name" => "Test Testerson",
            "address_country" => "United States",
            "address_country_code" => "US",
            "address_zip" => "95131",
            "address_state" => "CA",
            "address_city" => "San Jose",
            "address_street" => "123 any street",
            "business" => "seller@paypalsandbox.com",
            "receiver_email" => config('flowerbug.test.receiver_email'),
            "receiver_id" => config('flowerbug.test.receiver_email'),
            "residence_country" => "US",
            "item_name1" => "November 2017 Technique Class",
            "item_number1" => "technique201711",
            "quantity" => "1",
            "shipping" => "0.00",
            "tax" => "0.00",
            "mc_currency" => "USD",
            "mc_fee" => "0.42",
            "mc_gross" => "4.00",
            "mc_gross_1" => "4.00",
            "mc_handling" => "0.00",
            "mc_handling1" => "0.00",
            "mc_shipping" => "0.00",
            "mc_shipping1" => "0.00",
            "txn_type" => "cart",
            "txn_id" => "899327589",
            "notify_version" => "2.4",
            "custom" => "xyz123",
            "invoice" => "abc1234",
            "test_ipn" => "1",
            "verify_sign" => "A2S1fniRGsoquzRDbs4f5rc383f8AuW.ZcKqQlhWcLybbTso5QQiozpn",
        ];
        $response = $this->post('/api/ipn', $data);

        $response->assertStatus(200);
    }

    public function tearDown()
    {
        $client = SimpleDbClient::factory(['region' => config('flowerbug.aws_region')]);

        $client->deleteAttributes([
            'DomainName' => config('flowerbug.simpledb.ipn_messages_domain'),
            'ItemName' => '899327589',
        ]);
    }
}
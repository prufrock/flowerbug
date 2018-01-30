<?php namespace App\Http\Controllers;

use App\Domain\IpnMessage;
use App\Domain\PaymentProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IpnController {

  public function store(
    Request $request,
    PaymentProcessor $paymentProcessor,
    IpnMessage $ipnMessage
  ) {

    $ipnMessage->data = $request->all();
    $paymentProcessor->process($ipnMessage);
    return response('', Response::HTTP_OK);
  }
}

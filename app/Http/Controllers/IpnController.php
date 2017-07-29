<?php namespace App\Http\Controllers;

use App\Domain\Interfaces\PaymentProcessorInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IpnController {

  public function index(Request $request, PaymentProcessorInterface $paymentProcessor) {

    $paymentProcessor->process($request->all());
    return response('',Response::HTTP_OK);
  }
}

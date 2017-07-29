<?php namespace App\Http\Controllers;

use App\Domain\Interfaces\PaymentProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IpnController {

  public function index(Request $request, PaymentProcessor $paymentProcessor) {

    $paymentProcessor->process($request->all());
    return response('',Response::HTTP_OK);
  }
}

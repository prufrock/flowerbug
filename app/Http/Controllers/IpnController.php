<?php namespace App\Http\Controllers;

use App\Domain\Interfaces\PaymentProcessor;
use Illuminate\Http\Response;

class IpnController {

  public function index(PaymentProcessor $paymentProcessor) {

    $paymentProcessor->process();
    return response('',Response::HTTP_OK);
  }
}

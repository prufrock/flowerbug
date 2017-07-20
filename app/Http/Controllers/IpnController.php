<?php namespace App\Http\Controllers;

use Illuminate\Http\Response;

class IpnController {

  public function index($request, $messageFactory) {

    $message = $messageFactory->createFromRequest($request);

    if ($message->wasInvalid()) {
      return response('', Response::HTTP_BAD_REQUEST);
    }

    $message->save();

    $message->shipPurchases();

    return response('', Response::HTTP_OK);
  }
}

<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IpnController {

  public function index(Request $request) {
    $this->verifyIpnMessageWithPayPal($request);
    $this->storeIpnMessageWithDatabase($request);
    $this->sendTheCustomerTheProject($request);
    return response("");
  }

  private function verifyIpnMessageWithPayPal($request) {

  }

  private function storeIpnMessageWithDatabase($request) {

  }

  private function sendTheCustomerTheProject($request) {

  }
}
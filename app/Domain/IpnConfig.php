<?php namespace App\Domain;

class IpnConfig {

  public function getHeaders() {
    
    $validationHeader = "";
    $validationHeader .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $validationHeader .= "Content-Type: "
      . "application/x-www-form-urlencoded\r\n";
    $validationHeader .= "Content-Length: <contentlength>\r\n\r\n";
    return $validationHeader;
  }
  
  public function getCmd() {
    
    return 'cmd=_notify-validate';
  }
  
  public function getUrl() {
    
    return config('flowerbug.paypal.ipn_verify_url');
  }
  
  public function getPort() {
    
    return config('flowerbug.paypal.ipn_verify_port');
  }
  
  public function getTimeout() {
    
    return 30;
  }
  
  public function getValidatedResponse() {
    
    return 'VERIFIED';
  }
  
  public function getInvalidatedResponse() {
    
    return 'INVALID';
  }
}

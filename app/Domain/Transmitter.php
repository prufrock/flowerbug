<?php namespace App\Domain;

use Illuminate\Support\Facades\Config;

class Transmitter {

  private $ses;

  public function __construct($ses = NULL) {

    $this->ses = $ses;
  }

  public function transmit($destAddr, $message) {

    $source = Config::get('flowerbug.seller_address');
    $subject = Config::get('flowerbug.email_subject');

    $this->ses->sendEmail(
      $source
      ,
      Array(
        'ToAddresses' => [$destAddr, $source]
      )
      ,
      Array(
        'Subject' => [
          'Data' => $subject
        ,
          'Charset' => 'UTF-8'
        ]
      ,
        'Body' => [
          'Html' => [
            'Data' => $message,
            'Charset' => 'UTF-8'
          ]
        ]
      )
      ,
      ['ReplyToAddresses' => [$source]]
    );

    return true;
  }
}
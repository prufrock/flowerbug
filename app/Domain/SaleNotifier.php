<?php namespace App\Domain;

class SaleNotifier {

  public function notify($transmitter) {

    $expectedMessage = config('flowerbug.sale_message');
    $expectedMessage .=<<<MESSAGE
<br/><br/>
MESSAGE;

    foreach($transmitter->getProjects() as $project) {
      $expectedMessage .= "\n\n" . $project;
    }
    $expectedMessage .=<<<MESSAGE


<br/><br/><br/>


MESSAGE;

    $transmitter->transmit($expectedMessage);

    return true;
  }
}
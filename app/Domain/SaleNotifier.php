<?php namespace App\Domain;

class SaleNotifier {

  public function notify($transmitter) {

    $message = config('flowerbug.sale_message');
    $message .=<<<MESSAGE
<br/><br/>


MESSAGE;

    foreach($transmitter->getProjects() as $project) {

      $message .= $project->title;

      $message .= implode($project->getGuides(), "\n\n");
    }
    $message .=<<<MESSAGE


<br/><br/><br/>


MESSAGE;

    $transmitter->transmit($message);

    return true;
  }
}
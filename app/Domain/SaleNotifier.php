<?php namespace App\Domain;

class SaleNotifier {

  public function notify($transmitter) {

    $message = config('flowerbug.sale_message');
    $message .=<<<MESSAGE
<br/><br/>


MESSAGE;

    foreach($transmitter->getProjects() as $project) {

      $message .= $project->title . "<br/>\n";
      $baseUrl = "http://example.com/";
      $types = ['doc' => 'Microsoft Office Word', 'pdf' => 'Adobe Acrobat PDF', 'jpg' => 'Images'];

      foreach($types as $type => $title) {

        $message .= $title . "<br/>\n";
        foreach ($project->getGuides($type) as $guide) {

          $message .= "<a href=\"{$guide->getUrl()}\">{$guide->getName()}</a>" . "<br/>\n<br/><br/>\n\n";
        }
      }
    }
    $message .=<<<MESSAGE
<br/><br/><br/>


MESSAGE;

    $transmitter->transmit($message);

    return true;
  }
}
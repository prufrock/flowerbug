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
      $typeTitles = ['Microsoft Office Word','Adobe Acrobat PDF','Images'];

      foreach ($project->getGuides() as $guide) {

        $message .= array_shift($typeTitles) . "<br/>\n";
        $message .= "<a href=\"$baseUrl{$guide->filename}\">{$guide->filename}</a>" . "<br/>\n<br/><br/>\n\n";
      }
    }
    $message .=<<<MESSAGE
<br/><br/><br/>


MESSAGE;

    $transmitter->transmit($message);

    return true;
  }
}
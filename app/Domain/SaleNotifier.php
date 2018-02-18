<?php 

namespace App\Domain;

class SaleNotifier
{
    public function notify($orderFulFiller)
    {
        $message = config('flowerbug.sale_message');
        $message .= <<<MESSAGE
<br/><br/>


MESSAGE;

        foreach ($orderFulFiller->getProjects() as $project) {

            $message .= $project->getTitle()."<br/>\n";
            $types = [
                'doc' => 'Microsoft Office Word',
                'pdf' => 'Adobe Acrobat PDF',
                'jpg' => 'Images',
            ];

            foreach ($types as $type => $title) {

                if (($guides = $project->getGuides($type))->count() > 0) {

                    $message .= $title."<br/>\n";
                    foreach ($guides as $guide) {

                        $message .= "<a href=\"{$guide->getUrl()}\">{$guide->getName()}</a>"."<br/>\n";
                    }
                    $message .= "<br/><br/>\n";
                }
            }
        }
        $message .= <<<MESSAGE
<br/><br/><br/>


MESSAGE;

        $orderFulFiller->transmit($message);

        return true;
    }
}
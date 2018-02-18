<?php 

namespace App\Domain;

class OrderFullFiller
{
    private $transmitter;

    private $projects;

    private $buyersEmailAddress;

    private $saleNotifier;

    public function __construct(
        \App\Domain\Transmitter $transmitter,
        \App\Domain\SaleNotifier $saleNotifier
    ) {
        $this->transmitter = $transmitter;
        $this->saleNotifier = $saleNotifier;
    }

    public function fulfill($projects, $buyersEmailAddress)
    {
        $this->projects = $projects;
        $this->buyersEmailAddress = $buyersEmailAddress;

        return $this->saleNotifier->notify($this);
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function getBuyersEmailAddress()
    {
        return $this->buyersEmailAddress;
    }

    public function transmit($output)
    {
        return $this->transmitter->transmit($this->buyersEmailAddress, $output);
    }
}
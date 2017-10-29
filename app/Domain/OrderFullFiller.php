<?php namespace App\Domain;

class OrderFullFiller {

  private $transmitter;

  private $itemsPurchased;

  private $buyersEmailAddress;

  private $saleNotifier;

  public function __construct(
    \App\Domain\Transmitter $transmitter,
    \App\Domain\SaleNotifier $saleNotifier
  ) {

    $this->transmitter = $transmitter;
    $this->saleNotifier = $saleNotifier;
  }

  public function fulfill($itemsPurchased, $buyersEmailAddress) {

    $this->itemsPurchased = $itemsPurchased;
    $this->buyersEmailAddress = $buyersEmailAddress;

    $this->transmitter->setOrder($this);
    return $this->saleNotifier->notify($this);
  }

  public function getItemsPurchased() {

    return $this->itemsPurchased;
  }

  public function getBuyersEmailAddress() {

    return $this->buyersEmailAddress;
  }

  public function transmit($output) {

    return $this->transmitter->transmit($this->buyersEmailAddress, $output);
  }
}
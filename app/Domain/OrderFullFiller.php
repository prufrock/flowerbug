<?php namespace App\Domain;

class OrderFullFiller {

  private $transmitter;

  private $itemsPurchased;

  private $buyersEmailAddress;

  private $saleNotifier;

  public function __construct($transmitter, $saleNotifier) {

    $this->transmitter = $transmitter;
    $this->saleNotifier = $saleNotifier;
  }

  public function fulfill($itemsPurchased, $buyersEmailAddress) {

    $this->itemsPurchased = $itemsPurchased;
    $this->buyersEmailAddress = $buyersEmailAddress;

    $this->transmitter->setOrder($this);
    $this->saleNotifier->initialize($this);
    return $this->saleNotifier->notify();
  }

  public function getItemsPurchased() {

    return $this->itemsPurchased;
  }

  public function getBuyersEmailAddress() {

    return $this->buyersEmailAddress;
  }

  public function getTransmitter() {

    return $this->transmitter;
  }
}
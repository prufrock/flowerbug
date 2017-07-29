<?php namespace App\Domain\Interfaces;

interface PaymentProcessorInterface {

  public function process($payment);
}
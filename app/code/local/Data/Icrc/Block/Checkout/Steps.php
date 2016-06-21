<?php

class Data_Icrc_Block_Checkout_Steps extends Mage_Core_Block_Template
{
  private $_steps = null;
  public function getSteps() {
    if (is_null($this->_steps)) {
      $this->_steps = array();
      $this->_steps[1] = 'cart';
      $this->_steps[2] = 'checkout';
      $this->_steps[3] = 'review';
      $this->_steps[4] = 'payment';
    }
    return $this->_steps;
  }
}


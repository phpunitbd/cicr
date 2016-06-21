<?php

class Data_Icrc_Block_Shipping extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
  public function getShippingRates()
  {
    $res = parent::getShippingRates();
    if ($res)
      return $res;
    else
      return $this->_getDefaultShippingRates();
  }

  /**
   * There must be a better way to do that
   */
  protected function _getDefaultShippingRates()
  {
    $rates = array();
    $flat = new Varien_Object();
    $flat->setCode('flatrate_flatrate')->setMethodTitle('Fixed')->setPrice(0.42);
    $rates['Flat Rate'] = array($flat);
    $free = new Varien_Object();
    $free->setCode('freeshipping_freeshipping')->setMethodTitle('Free');
    $rates['Free Shipping'] = array($free);
    return $rates;
  }
}


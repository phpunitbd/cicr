<?php

class Data_Icrc_Model_Carrier_IcrcInternal
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'icrc_internal';
  const AUTO = 'icrc_internal_auto';
  const POUCH_LIMIT = 15;
  const weightUnitsByKg = 1000;
  
  const HQ_METHOD = 'hq';
  const POUCH_METHOD = 'pouch';
  const FRET_METHOD = 'fret';

  /**
   * Collect rates for this shipping method based on information in $request
   *
   * @param Mage_Shipping_Model_Rate_Request $data
   * @return Mage_Shipping_Model_Rate_Result
   */
  public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
    // skip if not enabled AND not in admin
    if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active') &&
        !Mage::app()->getStore()->isAdmin()) {
        return false;
    }
    // this object will be returned as result of this method
    // containing all the shipping rates of this method
    $result = Mage::getModel('shipping/rate_result');
    
    // create new instance of method rate
    $method_p = Mage::getModel('shipping/rate_result_method');
    // record carrier information
    $method_p->setCarrier($this->_code);
    $method_p->setCarrierTitle(Mage::getStoreConfig('carriers/' . $this->_code . '/title'));
    // record method information
    $method_p->setMethod(self::POUCH_METHOD);
    $method_p->setMethodTitle('Pouch');
    // rate cost is optional property to record how much it costs to vendor to ship
    $method_p->setCost(0);
    $method_p->setPrice(0);
    
    // create new instance of method rate
    $method_f = Mage::getModel('shipping/rate_result_method');
    // record carrier information
    $method_f->setCarrier($this->_code);
    $method_f->setCarrierTitle(Mage::getStoreConfig('carriers/' . $this->_code . '/title'));
    // record method information
    $method_f->setMethod(self::FRET_METHOD);
    $method_f->setMethodTitle('Fret');
    // rate cost is optional property to record how much it costs to vendor to ship
    $method_f->setCost(0);
    $method_f->setPrice(0);
    
    // create new instance of method rate
    $method_h = Mage::getModel('shipping/rate_result_method');
    // record carrier information
    $method_h->setCarrier($this->_code);
    $method_h->setCarrierTitle(Mage::getStoreConfig('carriers/' . $this->_code . '/title'));
    // record method information
    $method_h->setMethod(self::HQ_METHOD);
    $method_h->setMethodTitle('Headquarters');
    // rate cost is optional property to record how much it costs to vendor to ship
    $method_h->setCost(0);
    $method_h->setPrice(0);

    // add these rates to the result, order depending of weight
    $w = $request->getPackageWeight();
    if ($w < self::POUCH_LIMIT) {
      $result->append($method_p);
      $result->append($method_f);
    }
    else {
      $result->append($method_f);
      $result->append($method_p);
    }
    $result->append($method_h);
    
    return $result;
  }

  /**
   * This method is used when viewing / listing Shipping Methods with Codes programmatically
   */
  public function getAllowedMethods() {
    return array($this->_code => $this->getConfigData('name'));
  }

  public static function selectAuto($rates, $quote, $data) {
    $w = $quote->getShippingAddress()->getWeight() / self::weightUnitsByKg;
    $hq = (array_key_exists('icrc_hq', $data) && $data['icrc_hq'])
        || (array_key_exists('icrc_type', $data) && $data['icrc_type'] == 'unit');
    foreach ($rates as $r) {
      if ($hq) {
        if ($r->getMethod() == self::HQ_METHOD)
          return $r;
      } else {
        if ($w < self::POUCH_LIMIT && $r->getMethod() == self::POUCH_METHOD)
          return $r;
        if ($w >= self::POUCH_LIMIT && $r->getMethod() == self::FRET_METHOD)
          return $r;
      }
    }
    $method = Mage::getModel('shipping/rate_result_method');
    $method->setCarrier('icrc_internal');
    $method->setCarrierTitle(Mage::getStoreConfig('carriers/icrc_internal/title'));
    if ($hq) {
      $method->setMethod(self::HQ_METHOD);
      $method->setMethodTitle('Headquarters');
    }
    else {
      $method->setMethod(($w < self::POUCH_LIMIT) ? self::POUCH_METHOD : self::FRET_METHOD);
      $method->setMethodTitle(($w < self::POUCH_LIMIT) ? 'Pouch' : 'Fret');
    }
    $method->setCost(0)->setPrice(0);
    return $method;
  }
}


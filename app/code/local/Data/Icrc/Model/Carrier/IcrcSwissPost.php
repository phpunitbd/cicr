<?php

class Data_Icrc_Model_Carrier_IcrcSwissPost
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'icrc';

  const maxFreeShippingWeight = 1500;
  const weightUnitsByKg = 1000;

  /**
   * Collect rates for this shipping method based on information in $request
   *
   * @param Mage_Shipping_Model_Rate_Request $data
   * @return Mage_Shipping_Model_Rate_Result
   */
  public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
    // skip if not enabled
    if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
        return false;
    }

    // get necessary configuration values
    $handling = Mage::getStoreConfig('carriers/' . $this->_code . '/handling');

    // this object will be returned as result of this method
    // containing all the shipping rates of this method
    $result = Mage::getModel('shipping/rate_result');

    $methods = $this->_getMethods($request);

    foreach ($methods as $rMethod) {
      // create new instance of method rate
      $method = Mage::getModel('shipping/rate_result_method');
 
      // record carrier information
      $method->setCarrier($this->_code);
      $method->setCarrierTitle(Mage::getStoreConfig('carriers/' . $this->_code . '/title'));
 
      // record method information
      $method->setMethod($rMethod['code']);
      $method->setMethodTitle(Mage::helper('data_icrc')->__($rMethod['title']));
 
      // rate cost is optional property to record how much it costs to vendor to ship
      $method->setCost($rMethod['amount']);

      $method->setPrice($rMethod['amount'] + $handling);
 
      // add this rate to the result
      $result->append($method);
    }

    return $result;
  }

  protected function _getMethods(Mage_Shipping_Model_Rate_Request $request) {
    $this->_initPrices();

    $methods = array();
    $methods[] = array('code' => 'eco', 'title' => 'Economic', 'amount' => $this->_computeEconomicPrice($request));
    $methods[] = array('code' => 'prio', 'title' => 'Prioritaire', 'amount' => $this->_computePrioritairePrice($request));
    $methods[] = array('code' => 'eco_ret_rec', 'title' => 'Economic with Return receipt', 'amount' => $this->_computeEcoReturnReceiptPrice($request));
    $methods[] = array('code' => 'prio_ret_rec', 'title' => 'Prioritaire with Return receipt', 'amount' => $this->_computePrioReturnReceiptPrice($request));

    return $methods;
  }

  protected function _computeEconomicPrice(Mage_Shipping_Model_Rate_Request $request) {
    $allItems = $request->getAllItems();
    $donnationPrice = 0;
    foreach ($allItems as $item) {
      if (Mage::helper('data_icrc/product')->isDonation($item->getProduct())) {
        // Don't count donnations in package price, so donnation-only or donnation with free items
        // will qualify for free shipping
        $donnationPrice += $item->getRowTotal();
      }
    }
    if ($request->getPackageWeight() <= self::maxFreeShippingWeight &&
        ($request->getPackageValue() - $donnationPrice) <= 0)
      return 0; // If all items are free and total weight is less than 1.5kg, then economic shipping is free
    $w = $request->getPackageWeight() / self::weightUnitsByKg;
    $price = 0;
    if ($request->getDestCountryId() == 'CH') {
      if ($w == 0) $price = $this->s1eco;
      while ($w > 0) {
        if ($w > 30) {
          $price += $this->s30eco;
          $w -= 30;
        } else {
          if ($w >= 20) $price += $this->s30eco;
          elseif ($w < 1) $price += $this->s1eco;
          elseif ($w < 2) $price += $this->s2eco;
          elseif ($w < 5) $price += $this->s5eco;
          elseif ($w < 10) $price += $this->s10eco;
          elseif ($w < 20) $price += $this->s20eco;
          $w = 0;
        }
      }
    } else {
      if ($w == 0) $price = $this->i1eco;
      while ($w > 0) {
        if ($w > 30) {
          $price += $this->i30eco;
          $w -= 30;
        } else {
          if ($w >= 20) $price += $this->i30eco;
          elseif ($w < 1) $price += $this->i1eco;
          elseif ($w < 2) $price += $this->i2eco;
          elseif ($w < 5) $price += $this->i5eco;
          elseif ($w < 10) $price += $this->i10eco;
          elseif ($w < 20) $price += $this->i20eco;
          $w = 0;
        }
      }
    }
    return $price;
  }

  protected function _computePrioritairePrice(Mage_Shipping_Model_Rate_Request $request) {
    $w = $request->getPackageWeight() / self::weightUnitsByKg;
    $price = 0;
    if ($request->getDestCountryId() == 'CH') {
      if ($w == 0) $price = $this->s1prio;
      while ($w > 0) {
        if ($w > 30) {
          $price += $this->s30prio;
          $w -= 30;
        } else {
          if ($w >= 20) $price += $this->s30prio;
          elseif ($w < 1) $price += $this->s1prio;
          elseif ($w < 2) $price += $this->s2prio;
          elseif ($w < 5) $price += $this->s5prio;
          elseif ($w < 10) $price += $this->s10prio;
          elseif ($w < 20) $price += $this->s20prio;
          $w = 0;
        }
      }
    } else {
      if ($w == 0) $price = $this->i1prio;
      while ($w > 0) {
        if ($w > 30) {
          $price += $this->i30prio;
          $w -= 30;
        } else {
          if ($w >= 20) $price += $this->i30prio;
          elseif ($w < 1) $price += $this->i1prio;
          elseif ($w < 2) $price += $this->i2prio;
          elseif ($w < 5) $price += $this->i5prio;
          elseif ($w < 10) $price += $this->i10prio;
          elseif ($w < 20) $price += $this->i20prio;
          $w = 0;
        }
      }
    }
    return $price;
  }

  protected function _computeEcoReturnReceiptPrice(Mage_Shipping_Model_Rate_Request $request) {
    return $this->_computeEconomicPrice($request) + $this->receiptPrice;
  }

  protected function _computePrioReturnReceiptPrice(Mage_Shipping_Model_Rate_Request $request) {
    return $this->_computePrioritairePrice($request) + $this->receiptPrice;
  }

  protected $receiptPrice = 6;
  protected $i30eco = 164;
  protected $i20eco = 113;
  protected $i10eco = 77;
  protected $i5eco = 25;
  protected $i2eco = 15;
  protected $i1eco = 12;
  protected $i30prio = 200;
  protected $i20prio = 160;
  protected $i10prio = 103;
  protected $i5prio = 58;
  protected $i2prio = 17;
  protected $i1prio = 15;
  protected $s30eco = 27;
  protected $s20eco = 20;
  protected $s10eco = 15;
  protected $s5eco = 14;
  protected $s2eco = 12;
  protected $s1eco = 12;
  protected $s30prio = 30;
  protected $s20prio = 23;
  protected $s10prio = 17;
  protected $s5prio = 16;
  protected $s2prio = 14;
  protected $s1prio = 14;
  protected $pricesInitialized = false;

  protected function _initPrices() {
    if ($this->pricesInitialized) return $this;
    // TODO: load from DB, with admin config
    $this->pricesInitialized = true;
    return $this;
  }

  /**
   * This method is used when viewing / listing Shipping Methods with Codes programmatically
   */
  public function getAllowedMethods() {
    return array($this->_code => $this->getConfigData('name'));
  }

  public function getReceiptPrice() {
    return $this->_initPrices()->receiptPrice;
  }

  public function getDefaultPrices(Mage_Shipping_Model_Rate_Request $request) {
    $this->_initPrices();
    return array(
      'eco' => $this->_computeEconomicPrice($request),
      'prio' => $this->_computePrioritairePrice($request)
    );
  }
}


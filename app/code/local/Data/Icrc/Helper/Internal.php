<?php

class Data_Icrc_Helper_Internal extends Mage_Core_Helper_Abstract
{
  public function rewriteAllStepsBlock($onepage) {
    $onepage->unsetChild('billing');
    $shipping = $onepage->getChild('shipping');
    $shipping_method = $onepage->getChild('shipping_method');
    $payment = $onepage->getChild('payment');

    $shipping->setTemplate('checkout/onepage/internal/shipping.phtml');
    $shipping_method->setTemplate('checkout/onepage/internal/validation.phtml');

    return $onepage;
  }

  public function getUnitAdressData($unit) {
    // TODO?
    return array(
      'prefix' => ' ', // To make magento happy event if prefix is needed
      'firstname' => 'International Committee of the Red Cross',
      'lastname' => '.',
      'telephone' => '+41227346001',
      'city' => 'Geneva',
      'postcode' => '1202',
      'country_id' => 'CH',
      'street' => array('19 Avenue de la paix')
    );
  }

  private $_internalId = null;
  public function isInternal($storeId = null) {
    if ($this->_internalId === null)
      $this->_internalId = Mage::getModel('core/store')->load('internal', 'code')->getId();
    if ($storeId === null)
      return Mage::app()->getStore()->getId() == $this->_internalId;
    return $storeId == $this->_internalId;
  }
  
  private $_internalWebsiteId = null;
  public function getInternalWebsiteId() {
    if ($this->_internalWebsiteId === null)
      $this->_internalWebsiteId = Mage::getModel('core/website')->load('internal', 'code')->getId();
    return $this->_internalWebsiteId;
  }

  public function getInternalId() {
    if ($this->_internalId === null)
      $this->_internalId = Mage::getModel('core/store')->load('internal', 'code')->getId();
    return $this->_internalId;
  }

  public function getBillingInfo($quote, $title = true) {
    if (is_object($quote)) $qid = $quote->getId();
    else $qid = $quote;
    $attr = Mage::helper('data_icrc/attributes')->getOrderAttributes($qid);
    $ret = '<ul>';
    foreach ($attr as $a) {
      switch ($a->getTitle()) {
        case Data_Icrc_Helper_Attributes::BILLING_UNIT:
        case Data_Icrc_Helper_Attributes::BILLING_COST_CENTER:
        case Data_Icrc_Helper_Attributes::BILLING_OBJECTIVE_CODE:
        //case Data_Icrc_Helper_Attributes::BILLING_COMMENT:
          if ($title)
            $ret .= '<li><strong>' . $a->getTitle() . ':</strong> ' . $a->getValue() . '</li>';
          else
            $ret .= '<li>' . $a->getValue() . '</li>';
      }
    }
    $ret .= '</ul>';
    return $ret;
  }

  public function getBillingInfoPdf($quote, $title = true) {
    if (is_object($quote)) $qid = $quote->getId();
    else $qid = $quote;
    $attr = Mage::helper('data_icrc/attributes')->getOrderAttributes($qid);
    $ret = '';
    foreach ($attr as $a) {
      switch ($a->getTitle()) {
        case Data_Icrc_Helper_Attributes::BILLING_UNIT:
        case Data_Icrc_Helper_Attributes::BILLING_COST_CENTER:
        case Data_Icrc_Helper_Attributes::BILLING_OBJECTIVE_CODE:
        //case Data_Icrc_Helper_Attributes::BILLING_COMMENT:
          if ($title)
            $ret .= $a->getTitle() . ': ' . $a->getValue() . '|';
          else
            $ret .= $a->getValue() . '|';
      }
    }
    return $ret;
  }

  public function getShippingInfo($quote, $title = true, $showAddressForDelegation = false) {
    if (is_object($quote)) $qid = $quote->getId();
    else $qid = $quote;
    $attr = Mage::helper('data_icrc/attributes')->getOrderAttributes($qid);
    $ret = '<ul>';
    foreach ($attr as $a) {
      switch ($a->getTitle()) {
        case Data_Icrc_Helper_Attributes::SHIPPING_UNIT:
        //case Data_Icrc_Helper_Attributes::SHIPPING_COMMENT:
          if ($title)
            $ret .= '<li><strong>' . $this->_stripTitle($a->getTitle()) . ':</strong> ' . $a->getValue() . '</li>';
          else
            $ret .= '<li>' . $a->getValue() . '</li>';
          break;
        case Data_Icrc_Helper_Attributes::SHIPPING_TYPE:
          if ($showAddressForDelegation !== false && $a->getValue() == 'delegation')
            $address = $showAddressForDelegation;
          break;
      }
    }
    if (isset($address))
      $ret .= '<li>' . $address . '</li>';
    $ret .= '</ul>';
    return $ret;
  }

  public function getShippingInfoPdf($quote, $title = true) {
    if (is_object($quote)) $qid = $quote->getId();
    else $qid = $quote;
    $attr = Mage::helper('data_icrc/attributes')->getOrderAttributes($qid);
    $ret = '';
    foreach ($attr as $a) {
      switch ($a->getTitle()) {
        case Data_Icrc_Helper_Attributes::SHIPPING_UNIT:
        //case Data_Icrc_Helper_Attributes::SHIPPING_COMMENT:
          if ($title)
            $ret .= $this->_stripTitle($a->getTitle()) . ': ' . $a->getValue() . '|';
          else
            $ret .= $a->getValue() . '|';
          break;
      }
    }
    return $ret;
  }

  protected function _stripTitle($title) {
    if ($title == Data_Icrc_Helper_Attributes::SHIPPING_UNIT)
      return Data_Icrc_Helper_Attributes::BILLING_UNIT;
    if ($title == Data_Icrc_Helper_Attributes::SHIPPING_COMMENT)
      return Data_Icrc_Helper_Attributes::BILLING_COMMENT;
    return $title;
  }
  
  public function getRadarJson($type) {
    $ret = array();
    switch ($type) {
      case 'unit':
        $collection = Mage::getModel('data_icrc/radar_acronym')->getCollection();
        foreach ($collection as $item)
          $ret[$item->getCode()] = 1;
        break;
      case 'delegation':
        $collection = Mage::getModel('data_icrc/radar_delegation')->getCollection();
        foreach ($collection as $item)
          $ret[$item->getMainSiteName()] = 1;
        break;
      case 'unit-delegation':
        $collection = Mage::getModel('data_icrc/radar_acronym')->getCollection();
        foreach ($collection as $item)
          $ret[$item->getCode()] = 1;
        $collection = Mage::getModel('data_icrc/radar_delegation')->getCollection();
        foreach ($collection as $item)
          $ret[$item->getMainSiteName()] = 1;
        break;
      case 'cost-center':
        $collection = Mage::getModel('data_icrc/radar_costcenter')->getCollection();
        foreach ($collection as $item)
          $ret[$item->getCode()] = 1;
        break;
      case 'objective-code':
        $collection = Mage::getModel('data_icrc/radar_objective')->getCollection();
        foreach ($collection as $item)
          $ret[$item->getGOCode()] = 1;
        break;
    }
    return json_encode($ret);
  }
  
  private $_radarDataLoaded = false;
  public function getRadarValidatorData() {
    if ($this->_radarDataLoaded)
      return ;
    $this->_radarDataLoaded = true;
    $url = Mage::getUrl('icrc/radar/validator');
    echo "<script type=\"text/javascript\">
//<![CDATA[
  (function () {
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = '$url';
    var x = document.getElementsByTagName('script')[0];
    x.parentNode.insertBefore(s, x);
  })();
//]]>
</script>";
  }
}


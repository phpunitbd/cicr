<?php

class Data_Icrc_Model_Order_Address extends Mage_Sales_Model_Order_Address
{
  public function format($type)
  {
    //Data_Icrc_Helper_Debug::dump($this);
    if ($type == 'html' && $this->_isInternalOrder()) {
      if ($this->getOrder()->getShippingAddressId() == $this->getId())
        return $this->_formatInternalShipping();
      else
        return $this->_formatInternalBilling();
    }
    return parent::format($type);
  }
  
  protected function _isInternalOrder() {
    Data_Icrc_Helper_Debug::msg('store id: '.$this->getOrder()->getStoreId());
    return $this->getOrder()->getStoreId() == 
      Mage::getModel('core/store')->load('internal', 'code')->getId();
  }
  
  protected function _formatInternalShipping() {
    return Mage::helper('data_icrc/internal')->getShippingInfo($this->getOrder()->getQuoteId(), true, parent::format('html'));
  }
  
  protected function _formatInternalBilling() {
    return Mage::helper('data_icrc/internal')->getBillingInfo($this->getOrder()->getQuoteId());
  }
}


<?php

class Data_Icrc_Block_Customer_Billing extends Mage_Core_Block_Template
{
  private $_collection = null;
  function getInfoCollection($cid = null) {
    if ($this->_collection !== null)
      return $this->_collection;
    if ($cid === null)
      $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
    $collection = Mage::getModel('data_icrc/customer_billing_info')->getCollection();
    $collection->addFieldToFilter('customer_id', $cid)->load();
    return $this->_collection = $collection;
  }
}


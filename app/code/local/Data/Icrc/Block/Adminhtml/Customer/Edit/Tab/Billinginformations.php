<?php

class Data_Icrc_Block_Adminhtml_Customer_Edit_Tab_Billinginformations extends Mage_Adminhtml_Block_Template
                                                                      implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
  protected $_internalId = null;
  protected $_internalWebsiteId = null;
  
  public function __construct() {
    parent::__construct();
    $this->_internalId = Mage::helper('data_icrc/internal')->getInternalId();
    $this->_internalWebsiteId = Mage::helper('data_icrc/internal')->getInternalWebsiteId();
    $this->setTemplate('customer/edit/billing_info.phtml');
  }

  public function getTabLabel() {
    return Mage::helper('data_icrc')->__('Billing Informations');
  }

  public function getTabTitle() {
    return Mage::helper('data_icrc')->__('Billing Informations');
  }

  public function canShowTab() {
    $customer = Mage::registry('current_customer');
    if ($customer->getId() && $customer->getWebsiteId() == $this->_internalWebsiteId) {
      return true;
    }
    return false;
  }

  public function isHidden() {
    $customer = Mage::registry('current_customer');
    if (Mage::registry('current_customer')->getId() && $customer->getWebsiteId() == $this->_internalWebsiteId) {
      return false;
    }
    return true;
  }
  
  private $_collection = null;
  public function getInfoCollection($cid = null) {
    if ($this->_collection !== null)
      return $this->_collection;
    if ($cid === null)
      $cid = Mage::registry('current_customer')->getId();
    $collection = Mage::getModel('data_icrc/customer_billing_info')->getCollection();
    $collection->addFieldToFilter('customer_id', $cid)->load();
    return $this->_collection = $collection;
  }
  
  public function getNewUrl() {
    return Mage::helper("adminhtml")->getUrl("*/*/newBillingInfo");
  }
  
  public function getCustomerId() {
    return Mage::registry('current_customer')->getId();
  }
}

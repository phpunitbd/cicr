<?php

class Data_Icrc_Block_Payment_Icrc_Form extends Mage_Core_Block_Template
{
  public function __construct() {
    parent::__construct();
    $this->setTemplate('payment/icrc_info.phtml');
  }

  public function getMethodCode() {
    return $this->getMethod()->getCode();
  }

  protected function getAttrs() {
    if (!$this->getData('order_attrs')) {
      $_quote = $this->getMethod()->getInfoInstance()->getQuote();
      $_attrs = Mage::helper('data_icrc/attributes')->getOrderAttributes($_quote->getId(), true);
      $this->setData('order_attrs', $_attrs);
    }
    return $this->getData('order_attrs');
  }

  public function getDelegations() {
    foreach ($this->getAttrs() as $a) {
      if ($a->getTitle() == 'Unit or Delegation') $sel = $a->getValue();
    }
    $r = array('d1' => array('display' => 1, 'selected' => false), 'd2' => array('display' => 2, 'selected' => false));
    if (isset($sel) && array_key_exists($sel, $r))
      $r[$sel]['selected'] = true;
    return $r;
  }

  public function getUnits() {
    foreach ($this->getAttrs() as $a) {
      if ($a->getTitle() == 'Unit or Delegation') $sel = $a->getValue();
    }
    $r = array('u1' => array('display' => 1, 'selected' => false), 'u2' => array('display' => 2, 'selected' => false));
    if (isset($sel) && array_key_exists($sel, $r))
      $r[$sel]['selected'] = true;
    return $r;
  }

  public function getObjectiveCodes() {
    foreach ($this->getAttrs() as $a) {
      if ($a->getTitle() == 'Cost Center') $sel = $a->getValue();
    }
    $r = array(1 => array('display' => 1, 'selected' => false), 2 => array('display' => 2, 'selected' => false), 3 => array('display' => 3, 'selected' => false));
    if (isset($sel) && array_key_exists($sel, $r))
      $r[$sel]['selected'] = true;
    return $r;
  }

  public function getCostCenter() {
    foreach ($this->getAttrs() as $a) {
      if ($a->getTitle() == 'Objective Code') $sel = $a->getValue();
    }
    $r = array(1 => array('display' => 1, 'selected' => false), 2 => array('display' => 2, 'selected' => false), 3 => array('display' => 3, 'selected' => false));
    if (isset($sel) && array_key_exists($sel, $r))
      $r[$sel]['selected'] = true;
    return $r;
  }

  public function getComments() {
    foreach ($this->getAttrs() as $a) {
      if ($a->getTitle() == 'Comment') return $a->getValue();
    }
    return '';
  }
  
  private $_collection = null;
  public function getRegisteredBillingInfo($cid = null) {
    if ($this->_collection !== null)
      return $this->_collection;
    if ($cid === null)
      $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
    $collection = Mage::getModel('data_icrc/customer_billing_info')->getCollection();
    $collection->addFieldToFilter('customer_id', $cid)->load();
    return $this->_collection = $collection;
  }
  
  public function getRegisteredBillingInfoJSON($cid = null) {
    $collection = $this->getRegisteredBillingInfo($cid);
    $info = array();
    foreach ($collection as $i) {
      $info[$i->getId()] = array('unit' => $i->getUnit(),
                      'cost_center' => $i->getCostCenter(),
                      'objective_code' => $i->getObjectiveCode());
    }
    return json_encode($info);
  }
  
  private $_currentQuoteId = null;
  public function setCurrentQuote($quoteId) {
    $this->_currentQuoteId = $quoteId;
  }
  
  public function getOrderAttributes($quoteId = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    return Mage::helper('data_icrc/attributes')->getOrderAttributes($quoteId, true);
  }
  
  public function getCurrentBillingInfoId($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $attrs = $this->getOrderAttributes($quoteId);
    $helper = Mage::helper('data_icrc/attributes');
    $unit = $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::BILLING_UNIT, $quoteId, true);
    $oc = $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::BILLING_OBJECTIVE_CODE, $quoteId, true);
    $cc = $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::BILLING_COST_CENTER, $quoteId, true);
    if (empty($unit) || empty($cc) || empty($oc))
      return null;
    $collection = $this->getRegisteredBillingInfo($cid);
    foreach ($collection as $info) {
      if ($info->getUnit() == $unit &&
          $info->getObjectiveCode() == $oc &&
          $info->getCostCenter() == $cc)
        return $info->getId();
    }
    return null;
  }
  
  public function getCurrentBillingUnit($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::BILLING_UNIT, $quoteId, true);
  }
  
  public function getCurrentObjectiveCode($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::BILLING_OBJECTIVE_CODE, $quoteId, true);
  }
  
  public function getCurrentCostCenter($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::BILLING_COST_CENTER, $quoteId, true);
  }
  
  public function getCurrentBillingCom($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::BILLING_COMMENT, $quoteId, true);
  }
  
  public function getCurrentValidationEmail($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::VALIDATION_EMAIL, $quoteId, true);
  }
  
  public function getCurrentShippingUnit($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::SHIPPING_UNIT, $quoteId, true);
  }
  
  public function getCurrentShippingType($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::SHIPPING_TYPE, $quoteId, true);
  }
  
  public function getCurrentShippingCom($quoteId = null, $cid = null) {
    if ($quoteId === null) $quoteId = $this->_currentQuoteId;
    $helper = Mage::helper('data_icrc/attributes');
    return $helper->getOrderAttributeValue(Data_Icrc_Helper_Attributes::SHIPPING_COMMENT, $quoteId, true);
  }
}


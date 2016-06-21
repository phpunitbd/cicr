<?php

require_once 'Mage/Adminhtml/controllers/Sales/Order/CreateController.php';
class Data_Icrc_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Sales_Order_CreateController
{
  private $_internalId = null;
  protected function _isInternal() {
    if ($this->_internalId === null)
      $this->_internalId = Mage::getModel('core/store')->load('internal', 'code')->getId();
    Data_Icrc_Helper_debug::dump($this->_getSession()->getStoreId());
    return $this->_getSession()->getStoreId() == $this->_internalId;
  }
  
  protected function _processActionData($action = null) {
    if ($this->_isInternal())
      $this->_completeAddresses(true);
    parent::_processActionData($action);
    if (!$this->_isInternal())
      return $this;
    Data_Icrc_Helper_debug::msg('processActionData');
    $helper = Mage::helper('data_icrc/attributes');
    if ($paymentData = $this->getRequest()->getPost('payment')) {
      try {
        Data_Icrc_Helper_debug::dump($this->getRequest()->getPost('payment'));
        $helper->setIcrcPaymentInfoAdmin($this->_getQuote(), $paymentData);
        if (array_key_exists('method', $paymentData) && $paymentData['method'] == 'icrc') {
          // BillingAddress == ICRC
          $this->_getOrderCreateModel()->setBillingAddress(Mage::helper('data_icrc/internal')->getUnitAdressData(null));
        }
      }
      catch (Exception $e) {
        //Data_Icrc_Helper_debug::dump($e);
      }
    }
    if ($validationData = $this->getRequest()->getPost('validation')) {
      try {
        Data_Icrc_Helper_debug::dump($this->getRequest()->getPost('validation'));
        $helper->setIcrcValidationInfo($this->_getQuote(), $validationData);
      }
      catch (Exception $e) {
        //Data_Icrc_Helper_debug::dump($e);
      }
    }
    if ($shippingData = $this->getRequest()->getPost('shipping')) {
      try {
        Data_Icrc_Helper_debug::dump($this->getRequest()->getPost('shipping'));
        $helper->setIcrcShippingInfoAdmin($this->_getQuote(), $shippingData);
        if (array_key_exists('icrc_type', $shippingData) && $shippingData['icrc_type'] == 'unit') {
          $this->_getOrderCreateModel()->setShippingAddress(Mage::helper('data_icrc/internal')->getUnitAdressData(null));
          $this->_getOrderCreateModel()->collectShippingRates();
        }
      }
      catch (Exception $e) {
        //Data_Icrc_Helper_debug::dump($e);
      }
    }
    if ($this->getRequest()->getPost('collect_shipping_rates')) {
      $this->_getOrderCreateModel()->collectShippingRates();
    }
    return $this;
  }
  
  public function indexAction() {
    if ($this->_isInternal()) {
      $billing = $this->_getSession()->getQuote()->getBillingAddress();
      if ($billing->getPostcode() == null)
        $this->_getOrderCreateModel()->setBillingAddress(Mage::helper('data_icrc/internal')->getUnitAdressData(null));
      $shipping = $this->_getSession()->getQuote()->getShippingAddress();
      if ($shipping->getPostcode() == null)
        $this->_getOrderCreateModel()->setShippingAddress(Mage::helper('data_icrc/internal')->getUnitAdressData(null));
    }
    parent::indexAction();
  }
  
  public function saveAction() {
    if ($this->_isInternal()) {
      $this->_completeAddresses();
    }
    parent::saveAction();
  }
  
  protected function _completeAddresses($forceAddress = false) {
    $shipping = $this->getRequest()->getParam('shipping');
    $order = $this->getRequest()->getParam('order');
    if ($order === null)
      $order = array();
    if ($forceAddress ||
        (array_key_exists('billing_address', $order) && 
          (
            empty($order['billing_address']['prefix']) ||
            empty($order['billing_address']['firstname']) ||
            empty($order['billing_address']['lastname']) ||
            empty($order['billing_address']['telephone']) ||
            empty($order['billing_address']['city']) ||
            empty($order['billing_address']['postcode']) ||
            empty($order['billing_address']['country_id']) ||
            empty($order['billing_address']['street']))))
      $order['billing_address'] = Mage::helper('data_icrc/internal')->getUnitAdressData(null);
    if ($shipping && array_key_exists('icrc_type', $shipping) && $shipping['icrc_type'] == 'unit' && 
        ($forceAddress || !array_key_exists('shipping_address', $order)))
      $order['shipping_address'] = Mage::helper('data_icrc/internal')->getUnitAdressData(null);
    if (array_key_exists('shipping_address', $order) && empty($order['shipping_address']['prefix']))
      $order['shipping_address']['prefix'] = '-';
    $this->getRequest()->setParam('order', $order);
    $this->getRequest()->setPost('order', $order);
  }
}


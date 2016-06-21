<?php

class Data_Icrc_Model_Observer_Admin {

  public function __construct() {
    $this->en = Mage::getModel('core/store')->load('default', 'code')->getId();
    $this->fr = Mage::getModel('core/store')->load('fr', 'code')->getId();
    $this->int = Mage::getModel('core/store')->load('internal', 'code')->getId();
    $this->_icrc_address_data = array(
      'firstname' => 'International Committee of the Red Cross',
      'lastname' => 'ICRC',
      'street' => '19 Avenue de la paix',
      'city' => 'Geneva',
      'country_id' => 'CH',
      'telephone' => '+41 22 734 60 01',
      'postcode' => '1202'
    );
  }
  
  public function getIcrcAddress() { return $this->_icrc_address_data; }

  public function onUserAuthenticateBefore($eventData) {
    $session = Mage::getSingleton('core/session');
    //Data_Icrc_Helper_Debug::dump($eventData);
    //Data_Icrc_Helper_Debug::dump($eventData->getUser());
    //Data_Icrc_Helper_Debug::dump($session);

    $valid = $session->getData('isTotpValid', null);
    if ($valid === null) {
      $token = Mage::app()->getRequest()->getParam(Data_Icrc_Helper_Admin::TimeTokenParam);
      $valid = Mage::helper('data_icrc/admin')->checkAdminTimeToken($token);
    }
    if ($valid) return; // if TOTP is valid, then nothing to check
    if (Mage::getStoreConfig('icrc/janus/enable') == 0) return;
    // Else check if requested user as ICRC Admin role
    $user = Mage::getModel('admin/user')->loadByUsername($eventData->getUsername());
    if ($user->getRole()->getRoleName() == Data_Icrc_Helper_Admin::IcrcAdminRole)
      // Throw exception if role is ICRC Admin
      Mage::throwException('Cannot login with this role without a valid ticket from Janus');
  }

  // event: adminhtml_sales_order_create_process_data
  public function onCreateOrder($eventData) {
    $request = $eventData->getRequest();
    $order = $eventData->getOrderCreateModel();
    //Data_Icrc_Helper_Debug::dump($request, true);
    // Check if in internal store
    if (array_key_exists('store_id', $request)) {
      if ($request['store_id'] != $this->int) return;
      // check if there is an adress
      if ($order->getShippingAddress()->getCity() == null) {
        // Add ICRC address if not set
        //Data_Icrc_Helper_Debug::msg('setting ICRC address to admin create quote');
        $order->setBillingAddress($this->_icrc_address_data)->setShippingAsBilling(1);
      }
    }
    if (!array_key_exists('shipping', $request) ||
        !array_key_exists('validation', $request) ||
        !array_key_exists('payment', $request)) return;
    //Data_Icrc_Helper_Debug::dump($order->getQuote()->getData(), false);
    $quote = $order->getQuote();
    try {
      Mage::helper('data_icrc/attributes')
        ->setIcrcShippingInfo($quote, $request['shipping'])
        ->setIcrcValidationInfo($quote, $request['validation'])
        ->setIcrcPaymentInfo($quote, $request['payment']);
    } catch (Exception $e) {}
  }
}


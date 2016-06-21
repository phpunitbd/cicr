<?php

class Data_Icrc_Model_Payment_Pending extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_payment_pending';

  protected function _construct() {
    $this->_init('data_icrc/payment_pending');
  }

  public function sendEmail($customer) {
    $id = $this->getId();
    $email = $this->getEmail();
    $orderId = $this->getOrderId();
    if (!$id || empty($email))
    Mage::throwException('ICRC Payment not initialized');

    $order = Mage::getModel('sales/order');
    $order->load($orderId);

    $helper = Mage::helper('data_icrc/attributes');
    $attr = $helper->getOrderAttributes($order);

    foreach ($attr as $a) {
      switch ($a->getTitle()) {
        case Data_Icrc_Helper_Attributes::BILLING_UNIT: $bill_unit = $a->getValue(); break;
        case Data_Icrc_Helper_Attributes::BILLING_COST_CENTER: $bill_cc = $a->getValue(); break;
        case Data_Icrc_Helper_Attributes::BILLING_OBJECTIVE_CODE: $bill_oc = $a->getValue(); break;
        case Data_Icrc_Helper_Attributes::BILLING_COMMENT: $bill_com = $a->getValue(); break;
        case Data_Icrc_Helper_Attributes::SHIPPING_UNIT: $ship_unit = $a->getValue(); break;
        case Data_Icrc_Helper_Attributes::SHIPPING_COMMENT: $ship_com = $a->getValue(); break;
      }
    }
    if (!isset($ship_com)) $ship_com = null;
    if (!isset($bill_com)) $bill_com = null;

    $tmpl = Mage::getModel('core/email_template')->loadByCode('order_validation');
    $storeId = Mage::getModel('core/store')->load('internal', 'code')->getId();
    $tmpl->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name', $storeId));
    $tmpl->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email', $storeId));
    $variables = array(
      'ship_unit' => $ship_unit,
      'ship_com' => $ship_com,
      'bill_unit' => $bill_unit,
      'bill_cc' => $bill_cc,
      'bill_oc' => $bill_oc,
      'bill_com' => $bill_com,
      'validurl' => $this->getUrl(true),
      'refuseurl' => $this->getUrl(false),
      'order' => $order
    );
    $tmpl->send($email, null, $variables);

  }

  public function getUrl($validate) {
    $id = $this->getId();
    $token = $this->getToken();
    if (!$id || empty($token))
    Mage::throwException('ICRC Payment not initialized');
    return Mage::getUrl('icrc/payment/' . ($validate ? 'accept' : 'refuse'),
      array('_store' => 'internal',
          'id' => $id,
          'token' => $token));
  }
}

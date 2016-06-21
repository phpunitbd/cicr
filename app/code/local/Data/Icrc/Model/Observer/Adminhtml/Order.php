<?php

class Data_Icrc_Model_Observer_Adminhtml_Order
{
  public function createProcessData($eventData) {
    $request = $eventData->getRequest();
    $order = $eventData->getOrderCreateModel();

    //error_log('createProcessData:', 3, '/tmp/magento.html');
    //error_log(Zend_Debug::dump($request, null, false), 3, '/tmp/magento.html');
    //error_log(Zend_Debug::dump($order, null, false), 3, '/tmp/magento.html');

    //error_log('quote -> ' . $order->getQuote()->getId());

    $quote = $order->getQuote();

    if ($quote && $quote->getId() && array_key_exists('payment', $request) && is_array($request['payment']))
      Mage::helper('data_icrc/attributes')->setIcrcPaymentInfoAdmin($quote, $request['payment']);

  }
}


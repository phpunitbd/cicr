<?php

class Data_Icrc_Block_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
  /**
    * Adds 'icrc' method to 'no payment required' methods
    * @see Mage_Payment_Block_Form_Container::getMethods()
    */
  public function getMethods()
  {
    $methods = $this->getData('methods');

    if (is_null($methods)) {
      $quote = $this->getQuote();
      $store = $quote ? $quote->getStoreId() : null;
      $payment = $this->helper('payment');
      if ($quote->getGrandTotal() == 0 && Mage::helper('data_icrc/internal')->isInternal() == false) {
        // Consider we must pay because maybe we must pay at last
        $fake_quote = new Varien_Object();
        $fake_quote->setGrandTotal(1)->setStoreId($quote->getStoreId());
        $methods = $payment->getStoreMethods($store, $fake_quote);
        $total = 1;
      }
      else {
        $methods = $payment->getStoreMethods($store, $quote);
        $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
      }

      foreach ($methods as $key => $method) {
  
        if ($this->_canUseMethod($method)
            && ($total != 0
                || $method->getCode() == 'free'
                || $method->getCode() == 'icrc'
                || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles()))) {
          $this->_assignMethod($method);
        } else {
          unset($methods[$key]);
        }
      }

      $this->setData('methods', $methods);
    }
    return $methods;
  }
}


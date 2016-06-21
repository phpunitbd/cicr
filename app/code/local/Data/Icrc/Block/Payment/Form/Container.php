<?php

class Data_Icrc_Block_Payment_Form_Container extends Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form
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
      $methods = $this->helper('payment')->getStoreMethods($store, $quote);
      $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
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


<?php

class Data_Icrc_Model_Checkout_Session extends Mage_Checkout_Model_Session
{
  /**
   * Load data for customer quote and merge with current quote
   *
   * @return Mage_Checkout_Model_Session
   */
  public function loadCustomerQuote()
  {
    if (!Mage::getSingleton('customer/session')->getCustomerId()) {
      return $this;
    }

    Mage::dispatchEvent('load_customer_quote_before', array('checkout_session' => $this));

    $customerQuote = Mage::getModel('sales/quote')
        ->setStoreId(Mage::app()->getStore()->getId())
        ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());

    $allow_cart_merge = Mage::helper('persistent') && Mage::helper('persistent')->isShoppingCartPersist();
    if (!$allow_cart_merge && (!$this->hasQuote() || $this->getQuote()->getItemsCount() == 0))
      $allow_quote_load = true;
    else
      $allow_quote_load = $allow_cart_merge;

    if ($allow_quote_load && $customerQuote->getId() && $this->getQuoteId() != $customerQuote->getId()) {
      if ($this->getQuoteId()) {
        $customerQuote->merge($this->getQuote())
            ->collectTotals()
            ->save();
      }

      $this->setQuoteId($customerQuote->getId());

      if ($this->_quote) {
        $this->_quote->delete();
      }
      $this->_quote = $customerQuote;
    } else {
      $this->getQuote()->getBillingAddress();
      $this->getQuote()->getShippingAddress();
      $this->getQuote()->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
          ->setTotalsCollectedFlag(false)
          ->collectTotals()
          ->save();
    }
    return $this;
  }
}


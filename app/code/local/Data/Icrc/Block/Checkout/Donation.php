<?php

class Data_Icrc_Block_Checkout_Donation extends Mage_Core_Block_Template
{
  const PREFIX = 'donation-';

  protected $donations = null;
  function getDonations() {
    if ($this->donations === null)
      $this->donations = $this->_loadDonations();
    return $this->donations;
  }

  protected function _loadDonations() {
    $category = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'donations');
    if (!$category) return false;
    $donations = Mage::getResourceModel('catalog/product_collection')
                           ->addCategoryFilter($category);
    $result = array();
    $currency = Mage::app()->getStore()->getCurrentCurrency();
    $prefix = self::PREFIX . $currency->getCode();
    foreach ($donations as $p) {
      $don = Mage::getModel('catalog/product')->load($p->getId());
      if (strncmp($prefix, $don->getSku(), strlen($prefix)))
        continue;
      $price = (int)round($don->getProductionCost());
      if ($price == 0)
        continue;
      $donation = new Varien_Object();
      $fmt = $currency->formatTxt($price, array('precision' => 0));
      $donation->setPrice(preg_replace('/([0-9]+[\., 0-9]*)([^0-9]+)/','$1<span class="sup">$2</span>', $fmt))
               ->setUrl($this->getUrl('checkout/cart/add', array('product' => $p->getId(), 'qty' => 1, 'gotoCart' => 1)));
      $result[$price] = $donation;
    }
    ksort($result);
    return $result;
  }
}


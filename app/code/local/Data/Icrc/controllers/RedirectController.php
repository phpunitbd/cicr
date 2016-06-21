<?php

class Data_Icrc_RedirectController extends Mage_Core_Controller_Front_Action
{
  const NOTFOUND = 'noRoute';
  public function toAction()
  {
    $sku = $this->getRequest()->getParam('sku');
    $product = Mage::getModel('catalog/product');
    $productId = $product->getIdBySku($sku);
    $store = $this->getRequest()->getParam('___store');
    if ($store) Mage::App()->getCookie()->set('store', $store);
    if ($productId) {
      $product->load($productId);
      $this->_redirectUrl($product->getProductUrl());
    }
    else {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Product %s not found.', $sku));
      $notfound = Mage::getStoreConfig('icrc/redirect/notfound');
      if (empty($notfound))
        $notfound = self::NOTFOUND;
      $this->_forward($notfound, 'index', 'cms');
    }
  }
}


<?php

class Data_Icrc_VideoController extends Mage_Core_Controller_Front_Action
{
  const NOTFOUND = 'noRoute';
  public function viewAction()
  {
    $sku = $this->getRequest()->getParam('sku');
    $product = $this->_loadVideo($sku);
    if ($product) {
      $this->loadLayout(array('default'));
      $video = $this->getLayout()->getBlock('video.player');
      if ($video) $video->setProduct($product);
      $this->renderLayout();
    }
    else {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Product %s not found.', $sku));
      $notfound = Mage::getStoreConfig('icrc/redirect/notfound');
      if (empty($notfound))
        $notfound = self::NOTFOUND;
      $this->_forward($notfound, 'index', 'cms');
    }
  }

  public function viewAjaxAction()
  {
    $sku = $this->getRequest()->getParam('sku');
    $video = $this->getLayout()->createBlock('core/template');
    $video->setNoTitle(true);
    $product = $this->_loadVideo($sku);
    if ($product) {
      $video->setTemplate('catalog/video.phtml');
      $video->setProduct($product);
    }
    echo $video->renderView();
  }

  protected function _loadVideo($sku) {
    $product = Mage::getModel('catalog/product');
    $productId = $product->getIdBySku($sku);
    if ($productId) {
      $product->load($productId);
      return $product;
    }
    return null;
  }
}


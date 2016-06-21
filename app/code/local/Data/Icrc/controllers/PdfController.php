<?php

class Data_Icrc_PdfController extends Mage_Core_Controller_Front_Action
{
  const NOTFOUND = 'noRoute';
  public function viewAction()
  {
    $product = null;
    $id = $this->getRequest()->getParam('id');
    $sku = $this->getRequest()->getParam('sku');
    if ($id) $product = $this->_loadProduct($id);
    if (!$product) $product = $this->_loadProductSku($sku);
    if ($product && $product->pdf) {
		    $name = basename($product->pdf);
		    //recupérer le fichier		
		    $fileName = Mage::getBaseDir('media'). DS . 'catalog' . DS . 'product' . DS . 'pdf' . DS . $name;
		    if (file_exists($fileName)) {
		      $this->getResponse()->setHttpResponseCode(200);
		      if ($this->isIe()) {
		        $this->getResponse()->setHeader("Pragma", "public", true);
		        $this->getResponse()->setHeader("Expires", "0", true);
		        $this->getResponse()->setHeader("Cache-Control", "store, must-revalidate, post-check=0, pre-check=0", true);
		        $this->getResponse()->setHeader("Cache-Control", "private", false);
		      }
		      $this->getResponse()->setHeader('Content-type', 'application/pdf');
		      $this->getResponse()->setHeader('Content-Description', 'File Transfer');
		      $this->getResponse()->setHeader('Content-Transfert-Encoding', 'binary');
		      $filesize = filesize($fileName);
		      $this->getResponse()->setHeader('Content-Length', $filesize);
		      $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename="'.$name.'"');

          $this->getResponse()->clearBody();
          $this->getResponse()->sendHeaders();

          readfile($fileName);
		      exit(0);
		    }
    }
    else {
      if (empty($sku)) $sku = $id;
      if (!$product)
        Mage::getSingleton('core/session')->addError($this->__('Product %s not found.', $sku));
      else
        Mage::getSingleton('core/session')->addError($this->__('No PDF found for product %s.', $sku));
      $notfound = Mage::getStoreConfig('icrc/redirect/notfound');
      if (empty($notfound))
        $notfound = self::NOTFOUND;
      $this->_forward($notfound, 'index', 'cms');
    }
  }

  protected function _loadProduct($id) {
    $product = Mage::getModel('catalog/product');
    $product->load($id);
    if (!$product->getId())
      return null;
    return $product;
  }

  protected function _loadProductSku($sku) {
    $product = Mage::getModel('catalog/product');
    $productId = $product->getIdBySku($sku);
    if ($productId) {
      $product->load($productId);
      return $product;
    }
    return null;
  }
  
  protected function isIe() {
    return preg_match('/msie [0-9]/i', $_SERVER['HTTP_USER_AGENT']);
  }
}


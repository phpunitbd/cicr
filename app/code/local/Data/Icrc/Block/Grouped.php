<?php

class Data_Icrc_Block_Grouped extends Mage_Catalog_Block_Product_View_Type_Grouped
{
  public function getPrice() {
    $products = $this->getAssociatedProducts();
    $price = 0;
    foreach ($products as $product) {
      if ($product->isSalable())
        $price += $product->getPrice();
    }
    return $price;
  }
  
  public function getFinalPrice() {
    $products = $this->getAssociatedProducts();
    $price = 0;
    foreach ($products as $product) {
      if ($product->isSalable())
        $price += $product->getFinalPrice();
    }
    return $price;
  }
  
  public function getPriceBlockHtml($idSuffix = null) {
    $block = Mage::app()->getLayout()->createBlock('catalog/product_price');
    if ($idSuffix !== null) $block->setIdSuffix($idSuffix);
    $p = $this->getPrice();
    $fp = $this->getFinalPrice();
    $product = Mage::getModel('catalog/product')->load($this->getProduct()->getId());
    $product->setPrice($p)->setFinalPrice($fp)->setCanShowPrice(true);
    $block->setTemplate('catalog/product/price.phtml')->setProduct($product)->setInGrouped(false);
    return $block->toHtml();
  }
}


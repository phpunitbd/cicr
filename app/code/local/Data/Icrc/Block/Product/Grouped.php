<?php

class Data_Icrc_Block_Product_Grouped extends Mage_Core_Block_Template
{
  private $_product;
  public function getProduct() {
    if (!isset($this->_product)) {
      $info = $this->getLayout()->getBlock('product.info');
      $this->_product = $info ? $info->getProduct() : null;
    }
    return $this->_product;
  }
  
  private $ATTRIBUTES = array('name', 'lang', 'system');
  public function getAssociatedProductCollection() {
    $collection = $this->getProduct()->getTypeInstance()
                       ->getAssociatedProductCollection();
    foreach ($this->ATTRIBUTES as $attribute)
      $collection->addAttributeToSelect($attribute);
    return $collection;
  }
}

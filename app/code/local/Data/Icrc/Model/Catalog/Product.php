<?php

class Data_Icrc_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
  public function isSaleable() {
    return !$this->isOrphan() && parent::isSaleable();
  }
  
  private $__isOrphan;
  public function isOrphan() {
    //var_dump($this->getOrphan());
    if (!isset($this->__isOrphan)) {
      $this->__isOrphan = false;
      if ($this->getData('orphan')) {
        if (Mage::app()->getStore()->getId() == Mage::getModel('core/store')->load('default', 'code')->getId())
          $this->__isOrphan = true;
      }
    }
    return $this->__isOrphan;
  }
}


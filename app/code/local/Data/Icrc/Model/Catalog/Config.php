<?php

class Data_Icrc_Model_Catalog_Config extends Mage_Catalog_Model_Config
{
  public function getProductAttributes() {
    if (is_null($this->_productAttributes)) {
      parent::getProductAttributes();
      $icrc = array('languages_available');
      $this->_productAttributes = array_merge($this->_productAttributes, $icrc);
    }
    return $this->_productAttributes;
  }
}


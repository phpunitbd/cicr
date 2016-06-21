<?php

class Data_Icrc_Block_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
  function getOptions($attribute) {
    $config = Mage::helper('data_icrc/product')->getOptions($attribute, $this->getProduct(), $this->getCurrentStore(), true);
    $config = array_merge($config, $this->_getAdditionalConfig());
    return $config;
  }
}


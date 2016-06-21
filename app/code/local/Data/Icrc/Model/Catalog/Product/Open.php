<?php

class Data_Icrc_Model_Catalog_Product_Open extends Mage_Catalog_Model_Product
{
  public static function removeAttribute(Mage_Catalog_Model_Product &$object, $attribute) {
    unset($object->_data[$attribute]);
    unset($object->_origData[$attribute]);
  }
}


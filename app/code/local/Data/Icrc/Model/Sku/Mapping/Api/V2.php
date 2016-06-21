<?php

class Data_Icrc_Model_Sku_Mapping_Api_V2 extends Mage_Catalog_Model_Api_Resource
{
  public function skuMapping($skuMap) {
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $write->beginTransaction();
    try {
      Mage::getModel('data_icrc/sku_mapping')->deleteAll();
      foreach ($skuMap->link as $link) {
        $_link = Mage::getModel('data_icrc/sku_mapping');
        $_link->setMagentoSku($link->magento_sku)
              ->setAntalisSku($link->antalis_sku)
              ->save();
      }
      $write->commit();
    } catch (Exception $ex) {
      $write->rollback();
      throw $ex;
    }
    return 'ok';
  }
}


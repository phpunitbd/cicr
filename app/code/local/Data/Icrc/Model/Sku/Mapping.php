<?php

class Data_Icrc_Model_Sku_Mapping extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_sku_mapping';

  protected function _construct() {
    $this->_init('data_icrc/sku_mapping');
  }
  
  public function deleteAll() {
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $tableName = $resource->getTableName('data_icrc/sku_mapping');
    $writeConnection->query("DELETE FROM {$tableName}");
    return true;
  }
  
  public function loadByMagentoSku($sku) {
    $this->load($sku, 'magento_sku');
    return $this;
  }
  
  public function loadByAntalisSku($sku) {
    $this->load($sku, 'antalis_sku');
    return $this;
  }
}


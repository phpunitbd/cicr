<?php

class Data_Icrc_Model_Resource_Sku_Mapping extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct() {
    $this->_init('data_icrc/sku_mapping', 'entity_id');
  }
}


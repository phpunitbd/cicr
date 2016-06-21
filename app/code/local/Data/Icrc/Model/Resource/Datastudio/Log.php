<?php

class Data_Icrc_Model_Resource_Datastudio_Log extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct() {
    $this->_init('data_icrc/datastudio_log', 'entity_id');
  }
}


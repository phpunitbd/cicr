<?php

class Data_Icrc_Model_Resource_Radar_Delegation extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct() {
    $this->_init('data_icrc/radar_delegation', 'entity_id');
  }
}


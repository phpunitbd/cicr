<?php

class Data_Icrc_Model_Radar_Delegation extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_radar_delegation';

  protected function _construct() {
    $this->_init('data_icrc/radar_delegation');
  }
  
  public function getList() {
    return $this->getMainSiteName();
  }
}

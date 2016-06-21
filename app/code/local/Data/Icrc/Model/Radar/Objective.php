<?php

class Data_Icrc_Model_Radar_Objective extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_radar_objective';

  protected function _construct() {
    $this->_init('data_icrc/radar_objective');
  }
  
  public function getList() {
    return $this->getGOCode();
  }
}

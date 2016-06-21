<?php

class Data_Icrc_Model_Radar_Costcenter extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_radar_costcenter';

  protected function _construct() {
    $this->_init('data_icrc/radar_costcenter');
  }
  
  public function getList() {
    return $this->getCode();
  }
}

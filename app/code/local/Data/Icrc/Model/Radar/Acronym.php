<?php

class Data_Icrc_Model_Radar_Acronym extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_radar_acronym';

  protected function _construct() {
    $this->_init('data_icrc/radar_acronym');
  }
  
  public function getList() {
    return $this->getCode() . '<span class="informal"> ' . $this->getName() . '</span>';
  }
}

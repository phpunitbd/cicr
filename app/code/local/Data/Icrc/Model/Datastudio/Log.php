<?php

class Data_Icrc_Model_Datastudio_Log extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_datastudio_log';

  protected function _construct() {
    $this->_init('data_icrc/datastudio_log');
  }
}

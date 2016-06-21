<?php

class Data_Icrc_Model_Resource_Payment_Pending extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct() {
    $this->_init('data_icrc/payment_pending', 'authorize_id');
  }
}


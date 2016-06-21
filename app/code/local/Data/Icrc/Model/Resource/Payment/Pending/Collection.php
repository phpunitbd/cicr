<?php

class Data_Icrc_Model_Resource_Payment_Pending_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
  protected function _construct() {
    $this->_init('data_icrc/payment_pending');
  }
}


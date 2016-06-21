<?php

class Data_Icrc_Model_Resource_Customer_Billing_Info extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct() {
    $this->_init('data_icrc/customer_billing_info', 'entity_id');
  }
}


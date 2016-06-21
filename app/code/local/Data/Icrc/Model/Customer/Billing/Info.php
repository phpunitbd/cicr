<?php

class Data_Icrc_Model_Customer_Billing_Info extends Mage_Core_Model_Abstract
{
  protected $_eventPrefix = 'icrc_customer_billing_info';

  protected function _construct() {
    $this->_init('data_icrc/customer_billing_info');
  }
  
  public function deleteAll() {
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $tableName = $resource->getTableName('data_icrc/customer_billing_info');
    $writeConnection->query("DELETE FROM {$tableName}");
    return true;
  }
  
  public function loadByCustomerId($id) {
    $this->load($id, 'customer_id');
    return $this;
  }
  
  public function showInfo() {
    return htmlspecialchars($this->getUnit()) . ' : ' .
           htmlspecialchars($this->getCostCenter()) . ' : ' .
           htmlspecialchars($this->getObjectiveCode());
  }
}


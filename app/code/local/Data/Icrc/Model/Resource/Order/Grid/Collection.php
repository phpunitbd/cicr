<?php

class Data_Icrc_Model_Resource_Order_Grid_Collection extends Mage_Sales_Model_Resource_Order_Grid_Collection
{
  protected $_eavMode = false;

  public function setIsEavMode($value) { $this->_eavMode = $value; }
  
  public function getSelectCountSql()
  {

    if ($this->_eavMode) {
      $this->_renderFilters();

      $countSelect = clone $this->getSelect();
      $countSelect->reset(Zend_Db_Select::ORDER);
      $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
      $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
      $countSelect->reset(Zend_Db_Select::COLUMNS);

      $countSelect->columns('COUNT(*)');
    } else {
      $countSelect = parent::getSelectCountSql();
    }
    //error_log("here: " . ($this->_eavMode ? 'true' : 'false') . ' -> ' . $countSelect);

    return $countSelect;
  }
}


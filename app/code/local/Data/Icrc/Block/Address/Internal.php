<?php

class Data_Icrc_Block_Address_Internal extends Mage_Core_Block_Template
{
  protected function _toHtml() {
    if (!Mage::helper('data_icrc/internal')->isInternal())
      return '';
    return parent::_toHtml();
  }
  
  public function getTypeName() {
    if ($this->getPrefix()) {
      return $this->getPrefix() . '[icrc_type]';
    }
    else
      return 'icrc_type';
  }
  
  public function getTypeValue() {
    $address = $this->getAddress();
    if ($address)
      return $address->getIcrcType();
    return null;
  }
  
  public function getUnitName() {
    if ($this->getPrefix()) {
      return $this->getPrefix() . '[icrc_unit]';
    }
    else
      return 'icrc_unit';
  }
  
  public function getUnitValue() {
    $address = $this->getAddress();
    if ($address)
      return $address->getIcrcUnit();
    return null;
  }
  
  public function getCommentName() {
    if ($this->getPrefix()) {
      return $this->getPrefix() . '[icrc_com]';
    }
    else
      return 'icrc_com';
  }
  
  public function getAddress() {
    if (!isset($this->_address))
      $this->_address = $this->getParentBlock()->getAddress();
    return $this->_address;
  }
}

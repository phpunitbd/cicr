<?php

class Data_Icrc_Helper_Override_Customer_Address extends Mage_Customer_Helper_Address
{
  public function getAttributeValidationClass($attributeCode) {
    $class = array_filter(explode(' ', parent::getAttributeValidationClass($attributeCode)));
    $class[] = 'validate-latin-1';
    return implode(' ', array_unique($class));
  }
}


<?php

class Data_Icrc_Model_Sales_Order_Config extends Mage_Sales_Model_Order_Config
{
  public function getVisibleOnFrontStates()
  {
    return array_merge(parent::getVisibleOnFrontStates(), array('pending_payment'));
  }
}

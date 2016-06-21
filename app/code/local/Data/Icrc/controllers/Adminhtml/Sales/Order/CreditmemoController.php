<?php

require_once 'Mage/Adminhtml/controllers/Sales/Order/CreditmemoController.php';
class Data_Icrc_Adminhtml_Sales_Order_CreditmemoController extends Mage_Adminhtml_Sales_Order_CreditmemoController
{
  protected function _initCreditmemo($update = false) {
    return parent::_initCreditmemo($update)->setAllowZeroGrandTotal(true);
  }
}


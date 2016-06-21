<?php

class Data_Icrc_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address
{
  public function exportCustomerAddress() {
    $address = parent::exportCustomerAddress();
    foreach (array('icrc_type', 'icrc_unit') as $attr) {
      $val = $this->getData($attr);
      if ($val) {
        Data_Icrc_Helper_Debug::msgdump('exporting '.$attr.' from quote to customer address: ', $val);
        $address->setData($attr, $val);
      }
    }
    return $address;
  }
}


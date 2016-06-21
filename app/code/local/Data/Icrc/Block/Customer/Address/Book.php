<?php

class Data_Icrc_Block_Customer_Address_Book extends Mage_Customer_Block_Address_Book
{
  function getAddressHtml($address) {
    $html = parent::getAddressHtml($address);
    if (Mage::helper('data_icrc/internal')->isInternal()) {
      if ($address->getIcrcType() == 'unit')
        $moreInfo = 'U: ' . $address->getIcrcUnit();
      else
        $moreInfo = 'D: ' . $address->getIcrcUnit();
      return $html . '<br/>' . $moreInfo;
    } else {
      return $html;
    }
  }
}

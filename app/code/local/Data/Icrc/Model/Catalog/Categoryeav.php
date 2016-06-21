<?php

class Data_Icrc_Model_Catalog_Categoryeav extends Mage_Catalog_Model_Category
{
  protected function _construct()
  {
    $this->_init('catalog/category'); // Force using Eav over flat
  }
}

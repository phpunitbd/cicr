<?php

class Data_Icrc_Model_Category_Hack extends Mage_Catalog_Model_Category
{
  protected function _afterSave()
  {
    return Mage_Catalog_Model_Abstract::_afterSave();
  }
}


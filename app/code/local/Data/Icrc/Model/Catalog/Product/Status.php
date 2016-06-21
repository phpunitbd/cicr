<?php

class Data_Icrc_Model_Catalog_Product_Status extends Mage_Catalog_Model_Product_Status
{
  public function getFlatColums()
  {
    $attributeCode = $this->getAttribute()->getAttributeCode();
    $column = array(
        'unsigned'  => true,
        'default'   => null,
        'extra'     => null
    );

    if (Mage::helper('core')->useDbCompatibleMode()) {
      $column['type']     = 'tinyint';
      $column['is_null']  = true;
    } else {
      $column['type']     = Varien_Db_Ddl_Table::TYPE_SMALLINT;
      $column['nullable'] = true;
      $column['comment']  = 'Catalog Product Visibility ' . $attributeCode . ' column';
    }

    return array($attributeCode => $column);
  }

  public function getFlatUpdateSelect($store = null) {
    return Mage::getResourceSingleton('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
  }
}

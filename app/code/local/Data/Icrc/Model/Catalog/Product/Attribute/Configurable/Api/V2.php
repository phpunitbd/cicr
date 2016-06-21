<?php

class Data_Icrc_Model_Catalog_Product_Attribute_Configurable_Api_V2 extends Mage_Api_Model_Resource_Abstract
{
  public function info($productId, $attributeId) {
    $product = Mage::getSingleton('catalog/Product')->load($productId);

    $configurableAttributeData = $product->getTypeInstance()->getConfigurableAttributesAsArray($product) ;

    foreach($configurableAttributeData as $attributeData) {
      if ($attributeData['attribute_id'] == $attributeId)
        return $attributeData['id'];
    }
    return 'false';
  }
}


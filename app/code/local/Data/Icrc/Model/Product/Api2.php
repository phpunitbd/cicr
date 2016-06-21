<?php

class Data_Icrc_Model_Product_Api2 extends Mage_Catalog_Model_Product_Api_V2
{
  protected function _prepareDataForSave ($product, $productData)
  {
    Data_Icrc_Helper_Debug::msgdump('prepareDataForSave: ', $productData, false);
    $customAttributes = array('store_id');
    if (property_exists($productData, 'additional_attributes')) {
        foreach ($productData->additional_attributes as $_attrCode => $_attrValue)
          $productData->$_attrCode = $_attrValue;
    }
    if (property_exists($productData, 'website_ids')) {
			  if (is_array($productData->website_ids)) {
            $product->setWebsiteIds($productData->website_ids);
			  } elseif (is_numeric($productData->website_ids)) {
            $product->setWebsiteIds(array($productData->website_ids));
			  }
			  $customAttributes[] = 'website_ids';
        unset($productData->website_ids);
    }
    $unsetable_attributes = array('price', 'tax_class_id');
    foreach ($unsetable_attributes as $attr) {
      if (property_exists($productData, $attr) && $productData->$attr == 'FALSE') {
        $customAttributes[] = $attr;
        $productData->$attr = false;
      }
    }
    
    // Clean unused EAV attributes (see Data_Icrc_Model_Catalog_Product_Attribute_Media_Api2::cleanSaveProduct)
    if ($product->getStoreId() != Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
      Data_Icrc_Helper_Debug::msgdump('before clean: ', $product);
      $staticAttributes = array_keys(Mage::helper('data_icrc/product')->getStaticFields());
      $settedAttributes = array_keys(get_object_vars($productData));
      Data_Icrc_Helper_Debug::msgdump('settedAttributes: ', $settedAttributes);
      foreach ($product->getData() as $k => $v) {
        if (in_array($k, $staticAttributes))
          continue;
        if (in_array($k, $settedAttributes))
          continue;
        if (in_array($k, $customAttributes))
          continue;
        Data_Icrc_Model_Catalog_Product_Open::removeAttribute($product, $k);
      }
      Data_Icrc_Helper_Debug::msgdump('after clean: ', $product);
    }
    
    return parent::_prepareDataForSave ($product, $productData);
  }
}


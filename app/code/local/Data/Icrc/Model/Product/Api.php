<?php

class Data_Icrc_Model_Product_Api extends Mage_Catalog_Model_Product_Api
{
  protected function _prepareDataForSave ($product, $productData)
  {
    if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
      $product->setWebsiteIds($productData['website_ids']);
    }

    /*
     * Check for configurable products array passed through API Call
     */
    if(isset($productData['configurable_products_data']) && is_array($productData['configurable_products_data'])) {
      $product->setConfigurableProductsData($productData['configurable_products_data']);              
    }

    if(isset($productData['configurable_attributes_data']) && is_array($productData['configurable_attributes_data'])) {
      foreach($productData['configurable_attributes_data'] as $key => $data) {
        //Check to see if these values exist, otherwise try and populate from existing values
        $data['label'] =        (!empty($data['label'])) ? 
                $data['label'] : $product->getResource()->getAttribute($data['attribute_code'])->getStoreLabel();
        $data['frontend_label'] =       (!empty($data['frontend_label'])) ? 
                $data['frontend_label'] : $product->getResource()->getAttribute($data['attribute_code'])->getFrontendLabel();
        $productData['configurable_attributes_data'][$key] = $data;
      }
      $product->setConfigurableAttributesData($productData['configurable_attributes_data']);
      $product->setCanSaveConfigurableAttributes(1);
    }

    return parent::_prepareDataForSave ($product, $productData);
  }
}

<?php

$this->startSetup();

$a = array('status', 'lang', 'theme', 'author', 'languages_available');
foreach ($a as $attr) {
  $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attr);
  $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
  $attribute->setUsedInProductListing(1)->save();
}

$this->endSetup();


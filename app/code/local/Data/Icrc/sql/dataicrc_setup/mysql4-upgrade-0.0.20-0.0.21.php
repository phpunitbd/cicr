<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

function createAttributeSet3($setup, $attrSetCode) {
  try {
  $setup->removeAttributeSet('catalog_product', $attrSetCode);
  } catch (Exception $ex) {
    // Cannot delete as it does not exists
  }
  $attrSetId = $setup->getDefaultAttributeSetId('catalog_product');
  $attributeSetOld = Mage::getModel('eav/entity_attribute_set')->load($attrSetId);

  $entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');

  $attrSet = Mage::getModel('eav/entity_attribute_set');
  $attrSet->setAttributeSetName($attrSetCode);
  $attrSet->setEntityTypeId($entityType->getId());
  $attrSet->save();
  $attrSet->initFromSkeleton($attrSetId);
  $attrSet->save();
  //return $setup->getAttributeSetId('catalog_product', $attrSetCode); // won't work
  $newSet = Mage::getModel('eav/entity_attribute_set')->load($attrSetCode, 'attribute_set_name');
  return $newSet->getId();
}

$ex_set = createAttributeSet3($setup, 'exhibition');

$setup->endSetup();


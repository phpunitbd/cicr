<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

function recordAttr2($setup, $attributes, $set) {
  foreach ($attributes as $attl => $att) {
    if ($att[1] == null) {
      if ($set) {
        $attributeId = $setup->getAttribute('catalog_product', $att[0], 'attribute_id');
        //error_log("addAttributeToSet: $set ($attl:$attributeId)");
        $setup->addAttributeToSet('catalog_product', $set, 'ICRC Attributes', $attributeId);
      }
      continue;
    }
    //error_log('add attribute: '.$attl);
    $info = array(
      'type' => $att[2] ? $att[2] : 'varchar',
      'label' => $attl,
      'input' => $att[1],
      'required' => false,
      'user_defined' => true,
      'global' => true
    );
    if (!$set) $info['group'] = 'ICRC Attributes';
    $setup->addAttribute('catalog_product', $att[0], $info);
    $attributeId = $setup->getAttribute('catalog_product', $att[0], 'attribute_id');
    //error_log("*** got attributeId: $attributeId");
    if ($att[3]) {
      //error_log('set attribute visible: '.$attl);
      $setup->updateAttribute('catalog_product', $att[0], 
                              array('is_visible' => true, 'is_visible_on_front' => true, 'is_searchable' => true));
    }
    if (array_key_exists('add', $att) && is_array($att['add'])) {
      //error_log('additional');
      $opts = array();
      foreach ($att['add'] as $o => $v) {
        if (is_int($o)) $opts[$v] = true;
        else $opts[$o] = $v;
      }
      $setup->updateAttribute('catalog_product', $att[0], $opts);
    }
    if (array_key_exists('values', $att) && is_array($att['values'])) {
      //error_log('set values');
      $option = array();
      $option['attribute_id'] = $attributeId;
      foreach ($att['values'] as $label)
        $option['values'][] = $label;
      $setup->addAttributeOption($option);
    }
    if ($set) {
      //error_log("addAttributeToSet: $set ($attl)");
      $setup->addAttributeToSet('catalog_product', $set, 'ICRC Attributes', $attributeId);
      if (!$att[3]) $setup->updateAttribute('catalog_product', $att[0], array('is_configurable' => false));
    }
  }
}

function createAttributeSet2($setup, $attrSetCode) {
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

// LABEL => (CODE, INPUT(null = don't create, add in set), BACKEND(null = varchar), VISIBLE_ON_FRONT, 
//           'add' => (additional attributes to set at true), 'values' => (attribute values))

$donation_attributes = array(
'Currency' => array('currency', 'text', null, false)
);
$donation_set = createAttributeSet2($setup, 'donation');
recordAttr2($setup, $donation_attributes, $donation_set);

$ebook_attributes = array(
'Author' => array('author', null, null, true, 'add' => array('is_filterable')),
'Pdf link' => array('pdf', null, null, false),
'Number of pages' => array('page_number', null, null, true),
'Copyright' => array('copyright', null, null, false),
'People' => array('people', null, null, false, 'values' => array(1, 2, 3)),
'Places' => array('places', null, null, false, 'values' => array(1, 2, 3)),
'Bibliographical note' => array('biblio', null, 'text', true)
);
$ebook_set = createAttributeSet2($setup, 'ebook');
recordAttr2($setup, $ebook_attributes, $ebook_set);

$setup->endSetup();


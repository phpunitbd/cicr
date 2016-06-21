<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

function recordAttr($setup, $attributes, $set) {
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

function createAttributeSet($setup, $attrSetCode) {
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

$attributes = array(
'ISBN code' => array('isbn', 'text', null, true),
'Language code' => array('origlang', 'text', null, true),
'Language Name' => array('lang', 'select', null, true, 'values' => array('English', 'French', 'Spanish', 'Arabic', 'Russian', 'Chinese', 'Portuguese')),
'Date of release' => array('contentdate', 'date', 'datetime', true),
'Product theme' => array('theme', 'multiselect', null, true, 'values' => array(1, 2, 3), 'add' => array('is_filterable')),
'Storage location' => array('storloc', 'select', null, false, 'values' => array(1, 2, 3)),
'External Catalogue Y/N' => array('external_catalog', 'boolean', 'int', false),
'Product additional comments' => array('additional_comments', 'textarea', 'text', true),
'Production/cost unit price' => array('production_cost', 'price', null, false),
'Target audience' => array('target_audience', 'multiselect', null, true, 'values' => array(1, 2, 3))
);
recordAttr($setup, $attributes, null);

$publi_attributes = array(
'Author' => array('author', 'text', null, true, 'add' => array('is_filterable')),
'Dimensions (h)' => array('dimention_height', 'text', 'int', true),
'Dimensions (w)' => array('dimention_width', 'text', 'int', true),
'Dimensions (d)' => array('dimention_depth', 'text', 'int', true),
'Pdf link' => array('pdf', 'text', null, false),
'Number of pages' => array('page_number', 'text', null, true),
'Copyright' => array('copyright', 'text', null, false),
'People' => array('people', 'multiselect', null, false, 'values' => array(1, 2, 3)),
'Places' => array('places', 'multiselect', null, false, 'values' => array(1, 2, 3)),
'Bibliographical note' => array('biblio', 'textarea', 'text', true)
);
$publi_set = createAttributeSet($setup, 'publications');
recordAttr($setup, $publi_attributes, $publi_set);

$film_attributes = array(
'Download' => array('download_link', 'text', null, false),
'Streaming link' => array('streaming_link', 'text', null, false),
'Length' => array('length', 'text', null, true),
'Format' => array('film_format', 'select', null, true, 'values' => array('4:3', '16:9')),
'Definition' => array('film_definition', 'select', null, true, 'values' => array('HD', 'SD', '3D')),
'System' => array('film_system', 'select', null, true, 'values' => array('PAL', 'NTSC')),
'Additional material' => array('add_material', 'textarea', 'text', true),
'Copyright' => array('copyright', null, null, false),
'People' => array('people', null, null, true),
'Places' => array('places', null, null, true),
'Trailers' => array('trailer_link', 'text', null, false)
);
$film_set = createAttributeSet($setup, 'films');
recordAttr($setup, $film_attributes, $film_set);

$gift_attributes = array(
'Color' => array('color', null, null, null),
'Brand Name' => array('brand_name', 'text', null, true),
'Additional material' => array('add_material', null, null, true)
);
$gift_set = createAttributeSet($setup, 'gift');
recordAttr($setup, $gift_attributes, $gift_set);


error_log('end');

$setup->endSetup();


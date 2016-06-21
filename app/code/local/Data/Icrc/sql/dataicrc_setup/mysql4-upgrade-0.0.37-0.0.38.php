<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

function recordAttr_38($setup, $attributes, $set = null) {
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

$dimensions = array('dimensions', 'text', null, true);
recordAttr_38($setup, array('Dimensions' => $dimensions));

/* config */
$conf = new Mage_Core_Model_Config();
$conf->saveConfig('catalog/search/search_type', '3', 'default', 0); // combine

$setup->endSetup();


<?php

$this->startSetup();
$helper = Mage::helper('data_icrc/update');

list ($en, $fr, $int) = $helper->getStoreIds();

function attr_translation_40($code, $labels) {
  $attributeId = Mage::getResourceModel('eav/entity_attribute')
    ->getIdByCode('catalog_product', $code);
  $attr_model = Mage::getModel('catalog/resource_eav_attribute');
  $attr_model->load($attributeId);
  $attr_model->setStoreLabels($labels)->save();
  return $attr_model;
}

/* config */
$conf = new Mage_Core_Model_Config();
$conf->saveConfig('design/header/logo_src', 'images/Logo-ICRC.png', 'default', 0);
$conf->saveConfig('design/header/logo_src', 'images/Logo-CICR.png', 'stores', $fr);
$conf->saveConfig('design/header/logo_src', 'images/Logo-ICRC.png', 'stores', $int);

/** attribute translation **/
/* lang */
$attr_model = attr_translation_40('lang', array(0 => 'Language Name', $en => 'Language', $fr => 'Langue'));
$helper->doTranslateAttributeOptions($attr_model, $helper->TRANSLATE_LANG);

/* languages_available */
$attr_model = attr_translation_40('languages_available', array(0 => 'Language Name', $en => 'Language', $fr => 'Langue'));
$helper->doTranslateAttributeOptions($attr_model, $helper->TRANSLATE_LANG);

attr_translation_40('contentdate', array(0 => 'Date of release', $fr => 'Date de sortie'));
attr_translation_40('theme', array(0 => 'Product theme', $fr => ''));
attr_translation_40('isbn', array(0 => 'ISBN Code', $en => 'ISBN', $fr => 'ISBN'));
attr_translation_40('author', array(0 => 'Author', $fr => 'Auteur'));
attr_translation_40('dimensions', array(0 => 'Dimensions', $fr => 'Dimensions'));
attr_translation_40('people', array(0 => 'People', $fr => 'Personnes'));
attr_translation_40('places', array(0 => 'Places', $fr => 'Lieux'));
attr_translation_40('page_number', array(0 => 'Number of Pages', $fr => 'Nombre de pages'));
attr_translation_40('biblio', array(0 => 'Bibliographical Note', $fr => 'Notice bibliographique'));

$this->endSetup();


<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();
Mage::register('isSecureArea', true);

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'lang');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setStoreLabels(array(0 => 'Language Name', $en => 'Language', $int => 'Language', $fr => 'Langue'));
$attribute->save();

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'languages_available');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setStoreLabels(array(0 => 'Languages Available', $en => 'Languages', $int => 'Languages', $fr => 'Langues'));
$attribute->save();

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'film_system');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setStoreLabels(array(0 => 'System', $fr => 'Standard'));
$attribute->save();

$cats = Mage::getModel('catalog/category')->getCollection();
foreach ($cats as $cs) {
  $c = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId(0)->load($cs->getId());
  if (strcasecmp($c->getName(), 'E-Books') == 0) {
    $cat = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId($fr)->load($c->getId());
    if ($cat->getName() == "Livres électroniques") continue;
    $cat->setName("Livres électroniques")->save();
    continue;
  }
  if (strcasecmp($c->getName(), 'ICRC activities') == 0) {
    $cat = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId($fr)->load($c->getId());
    if ($cat->getName() == "Activités") continue;
    $cat->setName("Activités")->save();
    continue;
  }
  if (strcasecmp($c->getName(), 'General Information') == 0) {
    $cat = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId($fr)->load($c->getId());
    if ($cat->getName() == "Information générale") continue;
    $cat->setName("Information générale")->save();
    continue;
  }
}

// Do not forget to unset ...
Mage::unregister('isSecureArea');

$this->endSetup();



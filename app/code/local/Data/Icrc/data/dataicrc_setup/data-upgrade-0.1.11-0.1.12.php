<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();
Mage::register('isSecureArea', true);

$cats = Mage::getModel('catalog/category')->getCollection();
foreach ($cats as $cs) {
  $c = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId(0)->load($cs->getId());
  if (strcasecmp($c->getName(), 'E-Books') == 0) {
    $cat = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId($fr)->load($c->getId());
    $cat->setName("ebooks")->save();
    continue;
  }
  if (strcasecmp($c->getName(), 'Exhibitions') == 0) {
    $cat = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId($fr)->load($c->getId());
    $cat->setName("Expositions")->save();
    continue;
  }
}

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'film_system');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setStoreLabels(array(0 => 'System', $fr => 'Système'));
$attribute->save();

// Do not forget to unset ...
Mage::unregister('isSecureArea');

$this->endSetup();



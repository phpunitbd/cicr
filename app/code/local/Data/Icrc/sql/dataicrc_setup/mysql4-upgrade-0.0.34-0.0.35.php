<?php

$installer = $this;
$installer->startSetup();

$current_store = Mage::app()->getStore()->getCode();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

function _do_save_object_34_35($object) {
  // Here we disable update mode as it disable the ability to set store id in entities saving
  Mage::app()->setUpdateMode(false);
  $object->save();
  Mage::app()->setUpdateMode(true);
}

/// Now translate !!
$cats = Mage::getModel('catalog/category')->setStoreId(0)->getCollection();
$cats->addAttributeToSelect('name');
foreach ($cats as $c) {
  if (strcasecmp($c->getName(), 'General Information') == 0) {
    $tmp = Mage::getModel('catalog/category')->setStoreId($fr)->load($c->getId());
    $tmp->setStoreId($fr)->setName('Information générale');
    _do_save_object_34_35($tmp);
  }
  elseif (strcasecmp($c->getName(), 'ICRC Activities') == 0) {
    $tmp = Mage::getModel('catalog/category')->setStoreId($fr)->load($c->getId());
    $tmp->setStoreId($fr)->setName('Activités');
    _do_save_object_34_35($tmp);
  }
  elseif (strcasecmp($c->getName(), 'International Humanitarian Law') == 0) {
    $tmp = Mage::getModel('catalog/category')->setStoreId($fr)->load($c->getId());
    $tmp->setStoreId($fr)->setName('Droit humanitaire international');
    _do_save_object_34_35($tmp);
  }
}

Mage::app()->setCurrentStore($current_store);

$installer->endSetup();


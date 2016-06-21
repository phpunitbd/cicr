<?php

$this->startSetup();

$root = Mage::getModel('catalog/category')->load(2);
////// --------
$donations = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'donations');
$don_created = false;
if (!$donations || !$donations->getId()) {
  $don_created = true;
  $donations = Mage::getModel('catalog/category');
  $donations->setName('Donations')->setStoreId(0)
               ->setUrlKey('donations') 
               ->setIsActive(1)
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(0) 
               ->setPageTitle('Donations') 
               ->setIncludeInMenu(0)
               ->save() ;
}
$donations->setPath('1/2/'.$donations->getId())->save();
/*
if ($don_created) foreach (array(10, 20, 30, 40) as $don) {
  $product = Mage::getModel('catalog/product');
  $product->setName("$don euro donation")
          ->setWebsiteIDs(array(1))
          ->setWebsiteIds(array(1))
          ->setTypeId('simple')
          ->setSku($don . '-euro-donation')
          ->setDescription("$don euro donation")
          ->setShortDescription("$don euro donation")
          ->setPrice($don)
          ->setvisibility(0)
          ->setCategoryIds(array($donations->getId()))
          ->setWeight(0)
          ->setTaxClassId(0)
          ->save();
  $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
  $stockItem->setData('manage_stock', 0)->save();
}*/

//// *******************
$this->endSetup();


<?php

$installer = $this;
$installer->startSetup();

Mage::register('isSecureArea', true);
$current_store = Mage::app()->getStore()->getCode();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$en = Mage::getModel('core/store')->load('default', 'code')->getId();

function _do_save_object_33_34($object) {
  // Here we disable update mode as it disable the ability to set store id in entities saving
  Mage::app()->setUpdateMode(false);
  $object->save();
  Mage::app()->setUpdateMode(true);
}

$cats = Mage::getModel('catalog/category')->setStoreId(0)->getCollection();
$cats->addAttributeToSelect('name');
foreach ($cats as $c) {
  if ($c->getName() == 'Publications') $pub = $c;
  elseif ($c->getName() == 'Films') $films = $c;
  elseif ($c->getName() == 'E-Books') $ebooks = $c;
  elseif ($c->getName() == 'ICRC' || $c->getName() == 'About the ICRC') {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    Mage::app()->setUpdateMode(false);
    $urlkey = 'general-information';
    if (strpos('-pub', $tmp->getUrlKey()) !== false) $urlkey .= '-pub';
    elseif (strpos('-film', $tmp->getUrlKey()) !== false) $urlkey .= '-film';
    $tmp->setName('General Information')->setUrlKey($urlkey)->save();
    Mage::app()->setUpdateMode(true);
  }
  elseif (strcasecmp($c->getName(), 'Red Cross and Red Crescent Movement') == 0 ||
          strcasecmp($c->getName(), 'Red Cross Red Crescent Movement') == 0) {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->delete();
    continue;
  }
  // Remove erroneous "override default" on default store view
  $tmp = Mage::getModel('catalog/category')->load($c->getId());
  $tmp->setStoreId($en)->setName(false)->setUrlKey(false)->setIsActive(false)
      ->setThumbnail(false)->setDisplayMode(false);
  _do_save_object_33_34($tmp);
}

if (isset($ebooks)) {
  Mage::app()->setUpdateMode(false);
  $ebo_icrc_act = Mage::getModel('catalog/category');
  $ebo_icrc_act->setName('ICRC activities')->setStoreId(0)
               ->setUrlKey('icrc-activities-ebook') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('ICRC Activities') 
               ->setThumbnail('photos1.png')
               ->save() ;
  $ebo_icrc_act->setPath('1/2/'.$ebooks->getId().'/'.$ebo_icrc_act->getId())->save();

  $ebo_ihl = Mage::getModel('catalog/category');
  $ebo_ihl->setName('International humanitarian law')->setStoreId(0)
               ->setUrlKey('international-humanitarian-law-ebook') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('International Humanitarian Law') 
               ->setThumbnail('photos2.png')
               ->save() ;
  $ebo_ihl->setPath('1/2/'.$ebooks->getId().'/'.$ebo_ihl->getId())->save();

  $ebo_icrc = Mage::getModel('catalog/category');
  $ebo_icrc->setName('General Information')->setStoreId(0)
               ->setUrlKey('general-information-ebook') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('General Information') 
               ->setThumbnail('photos3.png')
               ->save() ;
  $ebo_icrc->setPath('1/2/'.$ebooks->getId().'/'.$ebo_icrc->getId())->save();
  Mage::app()->setUpdateMode(true);
}
else throw new Exception('no ebooks category');

Mage::app()->setCurrentStore($current_store);

$installer->endSetup();


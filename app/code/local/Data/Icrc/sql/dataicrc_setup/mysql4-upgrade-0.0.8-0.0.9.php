<?php

$this->startSetup();

$root = Mage::getModel('catalog/category')->load(2);
////// --------
$publications = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'publications');
$pub_created = false;
if (!$publications || !$publications->getId()) {
  $pub_created = true;
  $publications = Mage::getModel('catalog/category');
  $publications->setName('Publications')->setStoreId(0)
               ->setUrlKey('publications') 
               ->setIsActive(1)
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('Publications') 
               ->save() ;
}
$publications->setPath('1/2/'.$publications->getId())->save();

////// --------
$films = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'films');
$film_created = false;
if (!$films || !$films->getId()) {
  $film_created = true;
  $films = Mage::getModel('catalog/category');
  $films->setName('Films')->setStoreId(0)
               ->setUrlKey('films') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('Films') 
               ->save() ;
}
$films->setPath('1/2/'.$films->getId())->save();

////// --------
$gifts = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'gifts');
$gif_created = false;
if (!$gifts || !$gifts->getId()) {
  $gif_created = true;
  $gifts = Mage::getModel('catalog/category');
  $gifts->setName('Gifts')->setStoreId(0)
               ->setUrlKey('gifts') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('Gifts') 
               ->save() ;
}
$gifts->setPath('1/2/'.$gifts->getId())->save();

////// --------
$ebooks = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'e-books');
$eb_created = false;
if (!$ebooks || !$ebooks->getId()) {
  $eb_created = true;
  $ebooks = Mage::getModel('catalog/category');
  $ebooks->setName('E-Books')->setStoreId(0)
               ->setUrlKey('e-books') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('E-Books') 
               ->save() ;
}
$ebooks->setPath('1/2/'.$ebooks->getId())->save();

////// --------
$exhibitions = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'exhibitions');
$exhib_created = false;
if (!$exhibitions || !$exhibitions->getId()) {
  $exhib_created = true;
  $exhibitions = Mage::getModel('catalog/category');
  $exhibitions->setName('Exhibitions')->setStoreId(0)
               ->setUrlKey('exhibitions') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('Exhibitions') 
               ->save() ;
}
$exhibitions->setPath('1/2/'.$exhibitions->getId())->save();

//// *******************

$pub_icrc_act = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'icrc-activities-pub');
if ($pub_created || !$pub_icrc_act || !$pub_icrc_act->getId())
{
  $pub_icrc_act = Mage::getModel('catalog/category');
  $pub_icrc_act->setName('ICRC Activities')->setStoreId(0)
               ->setUrlKey('icrc-activities-pub') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('ICRC Activities') 
               ->setThumbnail('photos1.png')
               ->save() ;
  $pub_icrc_act->setPath('1/2/'.$publications->getId().'/'.$pub_icrc_act->getId())->save();
}

$pub_ihl = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'international-humanitarian-law-pub');
if ($pub_created || !$pub_ihl || !$pub_ihl->getId())
{
  $pub_ihl = Mage::getModel('catalog/category');
  $pub_ihl->setName('International Humanitarian Law')->setStoreId(0)
               ->setUrlKey('international-humanitarian-law-pub') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('International Humanitarian Law') 
               ->setThumbnail('photos2.png')
               ->save() ;
  $pub_ihl->setPath('1/2/'.$publications->getId().'/'.$pub_ihl->getId())->save();
}

$pub_icrc = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'general-information-pub');
if ($pub_created || !$pub_icrc || !$pub_icrc->getId())
{
  $pub_icrc = Mage::getModel('catalog/category');
  $pub_icrc->setName('General Information')->setStoreId(0)
               ->setUrlKey('general-information-pub') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('General Information') 
               ->setThumbnail('photos3.png')
               ->save() ;
  $pub_icrc->setPath('1/2/'.$publications->getId().'/'.$pub_icrc->getId())->save();
}

//// *******************

$film_icrc_act = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'icrc-activities-film');
if ($film_created || !$film_icrc_act || !$film_icrc_act->getId())
{
  $film_icrc_act = Mage::getModel('catalog/category');
  $film_icrc_act->setName('ICRC Activities')->setStoreId(0)
               ->setUrlKey('icrc-activities-film') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('ICRC Activities') 
               ->setThumbnail('photos1.png')
               ->save() ;
  $film_icrc_act->setPath('1/2/'.$films->getId().'/'.$pub_icrc_act->getId())->save();
}

$film_ihl = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'international-humanitarian-law-film');
if ($film_created || !$film_ihl || !$film_ihl->getId())
{
  $film_ihl = Mage::getModel('catalog/category');
  $film_ihl->setName('International Humanitarian Law')->setStoreId(0)
               ->setUrlKey('international-humanitarian-law-film') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('International Humanitarian Law') 
               ->setThumbnail('photos2.png')
               ->save() ;
  $film_ihl->setPath('1/2/'.$films->getId().'/'.$film_ihl->getId())->save();
}

$pub_icrc = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'icrc-film');
if ($pub_created || !$pub_icrc || !$pub_icrc->getId())
{
  $film_icrc = Mage::getModel('catalog/category');
  $film_icrc->setName('General Information')->setStoreId(0)
               ->setUrlKey('general-information-film') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('General Information') 
               ->setThumbnail('photos3.png')
               ->save() ;
  $film_icrc->setPath('1/2/'.$films->getId().'/'.$film_icrc->getId())->save();
}

//// *******************
$this->endSetup();


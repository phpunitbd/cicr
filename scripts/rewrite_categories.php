<?php

require_once('app/Mage.php');
Mage::app('admin');
Mage::register('isSecureArea', true);

$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();
$en = Mage::getModel('core/store')->load('default', 'code')->getId();

$root = Mage::getModel('catalog/category')->load(2);

$cats = Mage::getModel('catalog/category')->getCollection();
$cats->addAttributeToSelect('name')->addAttributeToSelect('path');
foreach ($cats as $c) {
  if ($c->getName() == 'Publications') { $pub = $c; $pubpath = '1/2/'.$pub->getId().'/'; }
  elseif ($c->getName() == 'Films') { $films = $c; $filmspath = '1/2/'.$films->getId().'/'; }
  elseif ($c->getName() == 'E-Books') { $ebooks = $c; $ebookspath = '1/2/'.$ebooks->getId().'/'; }
  elseif (isset($pubpath) && strncasecmp($c->getPath(), $pubpath, strlen($pubpath)) == 0) {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->delete();
  }
  elseif (isset($filmspath) && strncasecmp($c->getPath(), $filmspath, strlen($filmspath)) == 0) {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->delete();
  }
  elseif (isset($ebookspath) && strncasecmp($c->getPath(), $ebookspath, strlen($ebookspath)) == 0) {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->delete();
  }
  elseif ($c->getName() == 'ICRC') {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->setName('General Information')->save();
    $tmp->setStoreId($fr)->setName('Information générale')->save();
  }
  elseif ($c->getName() == 'ICRC activities') {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->setStoreId($fr)->setName('Activités')->save();
  }
  elseif ($c->getName() == 'International humanitarian law') {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->setStoreId($fr)->setName('Droit humanitaire international')->save();
  }
  elseif ($c->getName() == 'Red Cross and Red Crescent Movement') {
    $tmp = Mage::getModel('catalog/category')->load($c->getId());
    $tmp->delete();
  }
}

function do_subcategories($cat) {
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
  $ebo_icrc_act->setPath('1/2/'.$cat->getId().'/'.$ebo_icrc_act->getId())->save();
  //$ebo_icrc_act->setStoreId($fr)->setName('Activités')->save();

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
  $ebo_ihl->setPath('1/2/'.$cat->getId().'/'.$ebo_ihl->getId())->save();
  //$ebo_ihl->setStoreId($fr)->setName('Droit humanitaire international')->save();

  $ebo_icrc = Mage::getModel('catalog/category');
  $ebo_icrc->setName('General Information')->setStoreId(0)
               ->setUrlKey('gen-info-ebook') 
               ->setIsActive(1) 
               ->setIsAnchor(1)
               ->setPath('1/2') 
               ->setDisplayMode(Mage_Catalog_Model_Category::DM_MIXED) 
               ->setPageTitle('General Information') 
               ->setThumbnail('photos3.png')
               ->save() ;
  $ebo_icrc->setPath('1/2/'.$cat->getId().'/'.$ebo_icrc->getId())->save();
  //$ebo_icrc->setStoreId($fr)->setName('Information générale')->save();
}

if (isset($pub)) do_subcategories($pub);
else throw new Exception('no publications category');

if (isset($films)) do_subcategories($films);
else throw new Exception('no films category');

if (isset($ebooks)) do_subcategories($ebooks);
else throw new Exception('no ebooks category');

die(0);

$cats = Mage::getModel('catalog/category')->setStoreId(0)->getCollection();
$cats->addAttributeToSelect('name');
foreach ($cats as $c) {
  error_log($c->getName());
  if (strcasecmp($c->getName(), 'General Information') == 0) {
    error_log("here ... (GI)");
    $tmp = Mage::getModel('catalog/category')->setStoreId($fr)->load($c->getId());
    $tmp->setStoreId($fr)->setName('Information générale')->save();
  }
  elseif (strcasecmp($c->getName(), 'ICRC Activities') == 0) {
    error_log("here ... (IA)");
    $tmp = Mage::getModel('catalog/category')->setStoreId($fr)->load($c->getId());
    $tmp->setStoreId($fr)->setName('Activités')->save();
  }
  elseif (strcasecmp($c->getName(), 'International Humanitarian Law') == 0) {
    error_log("here ... (IHL)");
    $tmp = Mage::getModel('catalog/category')->setStoreId($fr)->load($c->getId());
    $tmp->setStoreId($fr)->setName('Droit humanitaire international')->save();
  }
}




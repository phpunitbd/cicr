<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRMATION_FLAG, '1', 'default', 0);

$root = Mage::getModel('catalog/category')->load(2);

$ref_med = Mage::getModel('catalog/category');
$ref_med->setName('Reference Publications: Medical')->setStoreId(0)
        ->setUrlKey('reference-publications-medical') 
        ->setPageTitle('Reference Publications: Medical');
cat_def_41($ref_med);

$ref_ihl = Mage::getModel('catalog/category');
$ref_ihl->setName('Reference Publications: Humanitarian')->setStoreId(0)
        ->setUrlKey('reference-publications-humanitarian')
        ->setPageTitle('Reference Publications: Humanitarian');
cat_def_41($ref_ihl);

$ref_edu = Mage::getModel('catalog/category');
$ref_edu->setName('Reference Publications: Academic')->setStoreId(0)
        ->setUrlKey('reference-publications-academic') 
        ->setPageTitle('Reference Publications: Academic');
cat_def_41($ref_edu);

$ref_law = Mage::getModel('catalog/category');
$ref_law->setName('Reference Publications: Law')->setStoreId(0)
        ->setUrlKey('reference-publications-law')
        ->setPageTitle('Reference Publications: Law') ;
cat_def_41($ref_law);

$ref_mil = Mage::getModel('catalog/category');
$ref_mil->setName('Reference Publications: Military')->setStoreId(0)
        ->setUrlKey('reference-publications-military')
        ->setPageTitle('Reference Publications: Military') ;
cat_def_41($ref_mil);

function cat_def_41($cat) {
  $cat->setImage(false)
      ->setThumbnail(false)
      ->setIsActive(1)
      ->setIsAnchor(1)
      ->setPath('1/2')
      ->setDisplayMode(0)
      ->setIncludeInMenu(0)
      ->save();
  $id = $cat->getId();
  $cat->setPath('1/2/' . $id)->save();
  foreach (array('varchar', 'int') as $t) {
    $tmp = "tmpmodent${t}cat${id}";
    $table = "catalog_category_entity_${t}";
    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "create temporary table $tmp as select ev.value_id from $table ev where
      ev.entity_id = $id and ev.store_id != 0 and not exists (
        select 1 from $table ev2 where 
          ev2.attribute_id = ev.attribute_id and ev.entity_id = ev2.entity_id and store_id = 0
      )
";
    $connection->query($sql);
    $sql = "update $table set store_id = 0 where value_id in (select value_id from $tmp)";
    $connection->query($sql);
    $sql = "delete from $table where entity_id = $id and store_id != 0";
    $connection->query($sql);
  }
}

$this->endSetup();


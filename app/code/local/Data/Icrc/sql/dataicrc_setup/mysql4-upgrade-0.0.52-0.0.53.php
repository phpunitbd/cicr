<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$fr_txt = '<ul>
<li><a href="reference-publications-medical.html">M&eacute;dical</a></li>
<li><a href="reference-publications-law.html">Juridique</a></li>
<li><a href="reference-publications-humanitarian.html">Humanitaire</a></li>
<li><a href="reference-publications-academic.html">Acad&eacute;mique</a></li>
<li><a href="reference-publications-military.html">Militaire</a></li>
</ul>';
$en_txt = '<ul>
<li><a href="reference-publications-medical.html">Medical</a></li>
<li><a href="reference-publications-law.html">Law</a></li>
<li><a href="reference-publications-humanitarian.html">Humanitarian</a></li>
<li><a href="reference-publications-academic.html">Academic</a></li>
<li><a href="reference-publications-military.html">Military</a></li>
</ul>';

$ref_mil = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'reference-publications-military');
if (!$ref_mil || !$ref_mil->getId()) {
  $ref_mil = Mage::getModel('catalog/category');
  $ref_mil->setName('Reference Publications: Military')->setStoreId(0)
          ->setUrlKey('reference-publications-military')
          ->setPageTitle('Reference Publications: Military') ;
  cat_def_53($ref_mil);
}

$blocks = Mage::getModel('cms/block')->getCollection()->addFilter('identifier', 'ref_pub');
foreach ($blocks as $b) {
  $stores = $b->getResource()->lookupStoreIds($b->getId());
  if ($stores[0] == $fr) $txt = $fr_txt;
  else $txt = $en_txt;
  $nb = count(explode('<li>', $b->getContent())) - 1;
  if ($nb == 4) {
    $tmp = Mage::getModel('cms/block')->load($b->getId());
    $tmp->setContent($txt)->save();
  }
}

$this->endSetup();

function cat_def_53($cat) {
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


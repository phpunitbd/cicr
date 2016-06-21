<?php

include_once('app/Mage.php');
Mage::app();

function createBlock($title, $store, $id)
{
  try {
    $staticBlock = array(
                'title' => $title,
                'identifier' => $id,
                'content' => 'Sample data for block '.$title,
                'is_active' => 1,
                'stores' => array($store)
                );
 
    Mage::getModel('cms/block')->setData($staticBlock)->save();
  }
  catch (Mage_Core_Exception $e) {
    // ignore
  }
}
$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

for ($i = 1; $i <= 4; $i++) {
  createBlock("Homepage Slide #$i for EN", $en, "index_slide_$i");
  createBlock("Homepage Slide #$i for FR", $fr, "index_slide_$i");
}
for ($i = 1; $i <= 4; $i++) {
  createBlock("Promo Block #$i for EN", $en, "index_foot_$i");
  createBlock("Promo Block #$i for FR", $fr, "index_foot_$i");
}






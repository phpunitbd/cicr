<?php

require_once 'abstract.php';

class Mage_Shell_Donation_Image extends Mage_Shell_Abstract
{
  public function run() {
    $current_store = Mage::app()->getStore()->getCode();

    $attrSetCollection = Mage::getModel("eav/entity_attribute_set")->getCollection();
    $attrSet = $attrSetCollection->addFieldToFilter("attribute_set_name", 'donation')->getFirstItem();
    $donations = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('attribute_set_id', $attrSet->getAttributeSetId());
    $file = Mage::getBaseDir('skin') . '/frontend/icrc/default/images/ICRC-cross-only.png';

    foreach ($donations as $donation) {
      $product = Mage::getModel('catalog/product')->setStore(0)->load($donation->getId());
      $mediaAttribute = array (
            'thumbnail',
            'small_image',
            'image'
      );
      $product->addImageToMediaGallery($file, $mediaAttribute, false, false);
      $product->save();
    }
  }
}

$sh = new Mage_Shell_Donation_Image();
$sh->run();


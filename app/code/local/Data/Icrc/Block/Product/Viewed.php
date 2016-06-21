<?php

class Data_Icrc_Block_Product_Viewed extends Mage_Reports_Block_Product_Viewed {
  // Overrides getItemsCollection to not include an ebook included as a publication
  public function getItemsCollection() {
    if (is_null($this->_collection)) {
      $helper = Mage::helper('data_icrc/product');
      $orig = $this->getPageSize();
      $this->setPageSize($orig * 2);
      $_collection = parent::getItemsCollection();
      $collection = new Varien_Data_Collection();
      foreach ($_collection as $item) {
        if ($helper->isEbook($item)) {
          $dup = $collection->getItemByColumnValue('name', $item->getName());
          if ($dup && $dup->getId()) {
            continue;
          }
        }
        if ($helper->isPublication($item)) {
          $dup = $collection->getItemByColumnValue('name', $item->getName());
          if ($dup && $dup->getId()) {
            if ($helper->isEbook($dup)) {
              $collection->removeItemByKey($dup->getId());
            }
          }
        }
        if ($collection->count() < $orig)
          $collection->addItem($item);
      }
      $this->_collection = $collection;
    }
    return $this->_collection;
  }
}


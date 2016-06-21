<?php

class Data_Icrc_Model_CatalogSearch_Layer extends Mage_CatalogSearch_Model_Layer
{
  /**
  * Handle orphan attribute if store is english
  */
  public function prepareProductCollection($collection) {
   	parent::prepareProductCollection($collection);
   	if (Mage::app()->getStore()->getId() == Mage::getModel('core/store')->load('default', 'code')->getId()) {
      $collection->addAttributeToFilter('orphan', array(array('null' => true), array('eq' => 0)), false);
   	}
    return $this;
  }

}


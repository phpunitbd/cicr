<?php

class Data_Icrc_Block_Adminhtml_Sales_Items_Renderer_Default extends Mage_Adminhtml_Block_Sales_Items_Renderer_Default
{
  public function canReturnItemToStock($item=null) {
    if (!is_null($item)) {
      if (!$item->hasCanReturnToStock()) {
        $product = Mage::getModel('catalog/product')->load($item->getOrderItem()->getProductId());
        if ( $product->getId() && $product->getStockItem()->getManageStock() ) {
          $item->setCanReturnToStock(true);
        } elseif ($product->isConfigurable()) {
          //Data_Icrc_Helper_Debug::dump($item->getData());
          $simple_product = Mage::getModel('catalog/product');
          $simple_product->load($simple_product->getIdBySku($item->getSku()));
          //Data_Icrc_Helper_Debug::dump($simple_product->getData());
          if ( $simple_product->getId() && $simple_product->getStockItem()->getManageStock() )
            $item->setCanReturnToStock(true)->setBackToStock(true);
          else
            $item->setCanReturnToStock(false);
        } else {
          $item->setCanReturnToStock(false);
        }
      }
    }
    return parent::canReturnItemToStock($item);
  }
}


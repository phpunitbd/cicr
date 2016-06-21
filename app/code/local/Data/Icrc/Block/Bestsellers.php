<?php
class Data_Icrc_Block_Bestsellers extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setMax(10);
    }

    public function _toHtml()
    {
        $storeId = Mage::app()->getStore()->getId();
        
        // Products reports sorted by ordered quantity (normal catalog products doesn't have ordered quantity)
        $products = Mage::getResourceModel('reports/product_collection')
            ->addOrderedLines()
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);
        
        // Creating array of bestselling products,
        // with conversion simple products to configurable products if possible
        $bestsellersIdsToQuantity = array();
        foreach ($products as $product) {
            $parentIds = Mage::getModel('catalog/product_type_configurable')
                ->getParentIdsByChild($product->getId());
            if (!empty($parentIds)) {
                $theId = $parentIds[0];
                //$theQty = $product->getOrderedQty();
                $theQty = $product->getCountLines();
            } else {
                $theId = $product->getId();
                //$theQty = $product->getOrderedQty();
                $theQty = $product->getCountLines();
            }
            if (!array_key_exists($theId, $bestsellersIdsToQuantity))
                $bestsellersIdsToQuantity[$theId] = $theQty;
            else
                $bestsellersIdsToQuantity[$theId] += $theQty;
        }
        
        // Sorting bestsellers by ordered quantity and getting their keys
        arsort($bestsellersIdsToQuantity);
        $bestsellersIds = array_keys($bestsellersIdsToQuantity);
        
        // Creating ordered array of bestsellers
        $bestsellers = array();
        foreach($bestsellersIds as $id) {
            $bestseller = Mage::getModel('catalog/product')->load($id);
            
            // Conditions to pass product
            $isVisible = in_array($bestseller->getVisibility(), array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, // visibility set to "catalog"
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, // visibility set to "catalog, search"
            ));
            $isActive = $bestseller->getStatus();
            
            if ($isVisible && $isActive) {
                $bestsellers[] = $bestseller;
            }
            
            // Limit of passed products count set to 10
            if (count($bestsellers) >= $this->getMax()) {
                break;
            }
        }
        
        $this->setProductCollection($bestsellers);

        return parent::_toHtml();
    }

    public function trace($msg) {
        $amsg = $this->getTraceMsg();
        if (empty($msg)) $amsg = "$msg\n";
        else $amsg .= "$msg\n";
        $this->setTraceMsg($amsg);
    }
}


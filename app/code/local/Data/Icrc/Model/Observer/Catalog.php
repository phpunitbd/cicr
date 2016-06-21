<?php

class Data_Icrc_Model_Observer_Catalog
{
  private $customData = array(
    'status' => array('type' => 'int'),
    'lang' => array(),
    'theme' => array(),
    'author' => array()
  );

  // Event: catalog_product_flat_prepare_columns
  public function productFlatPrepareColumns($eventData) {
    $columsData = $eventData->getColumns();
    if (!$columsData) return;
    $columns = $columsData->getColumns();
    if (!$columns || !is_array($columns)) return;
    //foreach ($columns as $name => $c) $name . ' -> ' . error_log(Zend_Debug::dump($c, null, false));
    foreach ($this->customData as $name => $data) {
      if (array_key_exists($name, $columns))
        continue;
      if (!array_key_exists('type', $data)) $data['type'] = 'varchar(255)';
      if (!array_key_exists('is_null', $data)) $data['is_null'] = true;
      if (!array_key_exists('default', $data)) $data['default'] = null;
      if (!array_key_exists('extra', $data)) $data['extra'] = null;
      if (!array_key_exists('unsigned', $data)) $data['unsigned'] = false;
      $columns[$name] = $data;
    }
    $columsData->setColumns($columns);
  }

  // Event: catalog_product_flat_rebuild
  public function productFlatRebuild($eventData) {
    $flatTableName = $eventData->getTable();
    $store = $eventData->getStoreId();
    $adapter = Mage::getSingleton('core/resource')->getConnection('core_setup');
    $describe      = $adapter->describeTable($flatTableName);
    foreach ($this->customData as $name => $data) {
      if (!isset($describe[$name])) 
        continue;
      try {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(4, $name);
        $select = Mage::getResourceModel('eav/entity_attribute')->getFlatUpdateSelect($attribute, $store);
        //error_log('attribute: ' . $name . ' - SQL: ' . $select->assemble());
        $sql = $select->crossUpdateFromSelect(array('e' => $flatTableName));
        $adapter->query($sql);
        //error_log('attribute: ' . $name . ' - SQL: ' . $sql);
      } catch (Exception $e) {}
    }
  }

  // Here we can add more multi-select arrays to serialize
  private static $icrc_attr = array('theme', 'target_audience', 'people', 'places', 'languages_available');
  // Event: catalog_product_save_before
	public function serializeCatalogProductMultiselectArrays($eventData) {
		$object = $eventData->data_object;	
		if (isset($object)) {
      $data = $object->getData();
      foreach (self::$icrc_attr as $k) {
        if (array_key_exists($k, $data) && is_array($data[$k])) {
          $object->setData($k, implode(',', $data[$k]));
        }
      }
		}
	}

  // Event: catalog_product_load_after
  public function onLoadProduct($eventData) {
		$object = $eventData->data_object;
		if (isset($object)) {
      //if ($object
    }
  }
  
  public function productListCollection($eventData) {
    $object = $eventData->getCollection();
    $dir = Mage::getSingleton('catalog/session')->getSortDirection();
    $sdir = $dir == 'desc' ? Varien_Data_Collection::SORT_ORDER_ASC : Varien_Data_Collection::SORT_ORDER_DESC;
    $object->addAttributeToSelect('contentdate')->addOrder('contentdate', $sdir)->resetData();
  }
  
  /**
   * 
   * @param Varien_Event_Observer $observer
   */
    public function saveProductDeleteBefore(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();
        $gmtDate = Mage::getSingleton('core/date')->gmtDate();
        foreach($product->getStoreIds() as $storeId)
        {
            Mage::getModel('data_icrc/productdeleted')
            ->setSku($product->getSku())
            ->setName($product->getName())
            ->setUpdatedAt($product->getUpdatedAt())
            ->setCreatedAt($product->getCreatedAt())
            ->setDeletedAt($gmtDate)
            ->setStoreId($storeId)
            ->save();
        }
    }
    
    public function setCustomAttribute(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        
        if($product->isConfigurable()) {
            $collection = Mage::getModel('catalog/product_type_configurable')
                ->getUsedProductCollection($product)
                ->addFilterByRequiredOptions()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                ->addAttributeToSort('contentdate','DESC')
                ->getFirstItem();
            
            $product->setData('contentdate', $collection->getContentdate());
            $product->getResource()->saveAttribute($product, 'contentdate');
            $product->setData('price_all', $product->getFinalPrice());
            $product->getResource()->saveAttribute($product, 'price_all');
            
        } elseif($product->isGrouped()) {
            $collection = Mage::getModel('catalog/product_type_grouped')
                ->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions()
                ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                ->addAttributeToSort('contentdate','DESC')
                ->getFirstItem();
            
            $product->setData('contentdate', $collection->getContentdate());
            $product->getResource()->saveAttribute($product, 'contentdate');
        } elseif($product->getTypeId() == "simple") {
            $parent_ids = Mage::getResourceSingleton('catalog/product_type_configurable')
                ->getParentIdsByChild($product->getId());
             
            if(count($parent_ids)) {
                foreach ($parent_ids as $id) {
                    $configurable = Mage::getModel('catalog/product')->load($id);
                    $collection = Mage::getModel('catalog/product_type_configurable')
                        ->getUsedProductCollection($configurable)
                        ->addFilterByRequiredOptions()
                        ->addAttributeToSelect("*")
                        ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                        ->addAttributeToSort('contentdate','DESC')
                        ->getFirstItem();
            
                    $configurable->setData('contentdate', $collection->getContentdate());
                    $configurable->getResource()->saveAttribute($configurable, 'contentdate'); 
                }
            }
            $parent_ids2 = Mage::getModel('catalog/product_type_grouped')
                ->getParentIdsByChild($product->getId());
            if(count($parent_ids2)) {
                foreach($parent_ids2 as $id) {
                    $grouped = Mage::getModel('catalog/product')->load($id);
                    $selections = Mage::getModel('catalog/product_type_grouped')
                        ->getAssociatedProductCollection($grouped)
                        ->addAttributeToSelect('*')
                        ->addFilterByRequiredOptions()
                        ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
                    if(count($selections)) {
                        $priceTotal = 0;
                        foreach($selections as $option) {
                            $priceTotal = $priceTotal + ($option->getFinalPrice() * $option->getQty());
                        }
                        $grouped->setData('price_all', $priceTotal);
                        $grouped->getResource()->saveAttribute($grouped, 'price_all');
                    }
                }
            }
            
            $product->setData('price_all', $product->getFinalPrice());
            $product->getResource()->saveAttribute($product, 'price_all');
        }
    }
}


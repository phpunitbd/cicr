<?php

class Data_Icrc_Model_Api2_Renderer_Xml_Writer
{

    /**
     * ROOT NODE
     */
    const XML_ROOT_NODE = 'IcrcEnvelope';

    /**
     * 
     * @param type $products
     * @return type
     */
    public function render($data)
    {
        $date = $this->getSession()->getData('date');
        $xml = new Data_Icrc_Model_Api2_Renderer_Xml_XMLExtended('<' . self::XML_ROOT_NODE . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" />');
        $Header = $xml->addChild("Header");
        $Header->addChild("DocumentDate", now());
        $Header->addChild("Date", $this->getSession()->getData('request_date'));
        $xml->addChild("MessageType", 'Product');
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $allStores = Mage::app()->getStores();
        foreach ($allStores as $store)
        {
            $storexml = $xml->addChild("Store");
            $_storeId = $store->getId();
            
            $updatedData = $this->getUpdateOrCreatedCollection($date, 'updated_at', $_storeId);
            $createdData = $this->getUpdateOrCreatedCollection($date, 'created_at', $_storeId);
            $deletedData = $this->getUpdateOrCreatedCollection($date, 'deleted_at', $_storeId);
            
            $storexml->addChild("StoreId", $_storeId);
            $operationUpdate = $storexml->addChild("Operation");
            $operationUpdate->addChild("OperationType", 'Update');
            $operationUpdate = $this->getXMLChilds($operationUpdate, $updatedData, 'updated_at');
            
            $operationCreate = $storexml->addChild("Operation");
            $operationCreate->addChild("OperationType", 'Create');
            $operationCreate = $this->getXMLChilds($operationCreate, $createdData, 'created_at');

            $operationDelete = $storexml->addChild("Operation");
            $operationDelete->addChild("OperationType", 'Delete');
            $operationDelete = $this->getXMLChildsdeleted($operationDelete, $deletedData, 'deleted_at');
        }
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $xmlString = $dom->saveXML();
        
        return $xmlString;
    }

    /**
     * 
     * @param type $Operation
     * @param type $products
     * @param type $type
     * @return type
     */
    protected function getXMLChilds($Operation, $products, $type) {
        foreach ($products as $data) {
            $product = $Operation->addChild("product");

            $instock = true ? "true" : "false";
            $status = $product->addChild("Status", $data['status']);
            $status->addAttribute('overloaded', $data['status_overloaded']);
            $visibility = $product->addChild("Visibility", $data['visibility']);
            $visibility->addAttribute('overloaded', $data['visibility_overloaded']);
            $product->addChild("updated_at", $data['updated_at']);
            $product->addChild("created_at", $data['created_at']);
            $product->addChild("SKU", $data['sku']);
            $product->addChild("attribsetName", $data['attribute_set_id']);
            $DescriptionData = $product->addChild("DescriptionData");
            $title = $DescriptionData->addChild("Title", $data['name']);
            $title->addAttribute('overloaded', $data['name_overloaded']);
            $description = $DescriptionData->addChild("Description", $data['description']);
            $description->addAttribute('overloaded', $data['description_overloaded']);
            $ShortDescription = $DescriptionData->addChild("ShortDescription", $data['short_description']);
            $ShortDescription->addAttribute('overloaded', $data['short_description_overloaded']);
            $ProductData = $product->addChild("ProductData");
            $ProductData->addChild("ProductType", $data['type_id']);
            $ProductData->addChild("category", $data['category_ids']);
            $ProductData->addChild("weight", $data['weight']);
            $ProductData->addChild("recent_import", $data['recent_import']);
            $url_key = $ProductData->addChild("url_key", $data['url_key']);
            $url_key->addAttribute('overloaded', $data['url_key_overloaded']);
            $prices = $ProductData->addChild("prices");

            if($data['calculated_price']) {
                $pricecalc = $prices->addChild("calculated_price", $data['calculated_price']);
                $pricecalc->addAttribute('overloaded', $data['calculated_price_overloaded']);
            } else {
                $price = $prices->addChild("price", $data['price']);
                $price->addAttribute('overloaded', $data['price_overloaded']);
                $special_price = $prices->addChild("special_price", $data['special_price']);
                $special_price->addAttribute('overloaded', $data['special_price_overloaded']);
                $special_from_date = $prices->addChild("special_from_date", $data['special_from_date']);
                $special_from_date->addAttribute('overloaded', $data['special_from_date_overloaded']);
                $special_to_date = $prices->addChild("special_to_date", $data['special_to_date']);
                $special_to_date->addAttribute('overloaded', $data['special_to_date_overloaded']);
                $tax_class_id = $prices->addChild("tax_class_id", $data['tax_class_id']);
                $tax_class_id->addAttribute('overloaded', $data['tax_class_id_overloaded']);
            }

            $IcrcAttributes = $ProductData->addChild("IcrcAttributes");
            foreach ($data['icrc_attribute_group'] as $icrcAttribute) {
                $attIcrc = $IcrcAttributes->addChild($icrcAttribute, $data[$icrcAttribute]);
                if($data[$icrcAttribute.'_overloaded'])
                {
                    $attIcrc->addAttribute('overloaded', $data[$icrcAttribute.'_overloaded']);
                }
            }
            $childs = $product->addChild("childs");
            if ($data['type_id'] == 'configurable') {
                $childs = $this->addproductChilds($data['configurable_product_options'], $childs, 'configurable');
            } elseif ($data['type_id'] == 'grouped') {
                $childs = $this->addproductChilds($data['grouped_associated_products'], $childs, 'grouped');
            }
            if($data['upsell_products']) {
                $upsellbalise = $product->addChild("upsell");
                foreach ($data['upsell_products'] as $upsell) {
                    $reProduct = $upsellbalise->addChild('Product');
                    $reProduct->addChild('SKU', $upsell);
                }
            }
            if($data['related_products']) {
                $relatedbalise = $product->addChild("related");
                foreach ($data['related_products'] as $related) {
                    $reProduct = $relatedbalise->addChild('Product');
                    $reProduct->addChild('SKU', $related);
                }
            }
            if($data['crosssell_products']) {
                $crosssellbalise = $product->addChild("crosssell");
                foreach ($data['crosssell_products'] as $crosssell) {
                    $reProduct = $crosssellbalise->addChild('Product');
                    $reProduct->addChild('SKU', $crosssell);
                }
            }
            if($data['image_gallery'])
            {
                $imageGallery = $product->addChild("Images");
                foreach ($data['image_gallery'] as $img) {
                    $image = $imageGallery->addChild('image');
                    if($img['disabled'] == 1) {
                        $image->addAttribute('exclude', 'true');
                    } else {
                        $image->addAttribute('exclude', 'false');
                    }
                    $type = $image->addChild('types');

                    if($img['file'] == $data['image'])
                    {
                        $type->addChild('type','base_image');
                    }
                    if($img['file'] == $data['small_image'])
                    {
                        $type->addChild('type','small_image');
                    }
                    if($img['file'] == $data['thumbnail'])
                    {
                        $type->addChild('type','thumbnail_image');
                    }
                    $image->addChild('url', $img['url']);
                }
            }
        }
        return $Operation;
    }
    
    protected function getXMLChildsdeleted($Operation, $products, $type) {
        foreach ($products as $data) {
            $product = $Operation->addChild("product");
            $product->addChild("SKU", $data['sku']);
            $product->addChild("updated_at", $data['updated_at']);
            $product->addChild("created_at", $data['created_at']);
            $product->addChild("deleted_at", $data['deleted_at']);
        }
        return $Operation;
    }
    
    public function getUpdateOrCreatedCollection($date, $type, $storeId) {
        if($type == "deleted_at")
        {
            $collection = Mage::getResourceModel('data_icrc/productdeleted_collection')
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter(
                    $type,
                    array('from' => $date)
                );
            
            $data = array();
            
            foreach ($collection as $product) {
                $productData['sku'] = $product->getSku();
                $productData['name'] = $product->getName();
                $productData['updated_at'] = $product->getUpdatedAt();
                $productData['created_at'] = $product->getCreatedAt();
                $productData['deleted_at'] = $product->getDeletedAt();
                
                $data[] = $productData;
            }
        } else {
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addStoreFilter($storeId)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(
                    $type,
                    array('from' => $date)
                );

            $data = array();

            foreach ($collection as $product) {
                $productbase = Mage::getModel('catalog/product')
			->setStoreId(0)
			->load($product->getId());
                
                $product = Mage::getModel('catalog/product')
			->setStoreId($storeId)
			->load($product->getId());
                
                $productData = $this->getAttributes($product, $productbase);
                if ($productData['type_id'] == 'configurable') {
                    $productData['configurable_product_options'] = $this->CollectValuesOptionsOfConfigproduct($product);
                } elseif ($productData['type_id'] == 'grouped') {
                    $productData['grouped_associated_products'] = $this->getGroupedAssociatedProducts($product);
                }
                $productData['upsell_products'] = $this->getUpsellProducts($product);
                $productData['related_products'] = $this->getRelatedProducts($product);
                $productData['crosssell_products'] = $this->getCrosssellProducts($product);
                $productData['image_gallery'] = $this->getImageGallery($product);
                $productData['attribute_set_id'] = $this->getAttributeSetName($productData['attribute_set_id']);
                $productData['request_date'] = $date;
                $productData['category_ids'] = implode(',', $product->getCategoryIds());
                $data[] = $productData;
            }
        }
        $this->getSession()->setData('request_date', $date);
        return $data;
    }

    protected function getproductChildsInfos($productId) {
        $childrenIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($productId);
        return $childrenIds[0];
    }

    protected function CollectOptionsOfConfigproduct($product) {
        // Collect options applicable to the configurable product
        $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        $attributeOptions = array();
        foreach ($productAttributeOptions as $productAttribute) {
            foreach ($productAttribute['values'] as $attribute) {
                $attributeOptions[$productAttribute['attribute_code']][$attribute['value_index']] = $attribute['store_label'];
            }
        }

        return $attributeOptions;
    }

    protected function CollectValuesOptionsOfConfigproduct($product) {
        $productChilds = $this->getproductChildsInfos($product->getId());
        $productAttributeOptions = $this->CollectOptionsOfConfigproduct($product);

        $values = array();
        foreach ($productChilds as $child) {
            $pr = Mage::getModel('catalog/product')->load($child);
            foreach ($productAttributeOptions as $label => $productAttribute) {
                $value_index = $pr->getData($label);
                $values[$pr->getSku()][$label] = $productAttribute[$value_index];
            }
        }
        return $values;
    }
    
    protected function getUpsellProducts($product) {
        $upsellProduct = array();
        foreach ($product->getUpSellProducts() as $upsell) {
            $upsellProduct[] = $upsell->getSku();
        }
        return $upsellProduct;
    }

    protected function getRelatedProducts($product) {
        $relatedProduct = array();
        foreach ($product->getRelatedProducts() as $related) {
            $relatedProduct[] = $related->getSku();
        }
        return $relatedProduct;
    }
    
    protected function getImageGallery($product)
    {
        $images = array();
        foreach ($product->getMediaGalleryImages() as $image)
        {
            $images[] = $image;
        }
        return $images;
    }

    protected function getCrosssellProducts($product) {
        $crosssellProduct = array();
        foreach ($product->getCrossSellProducts() as $crsProduct) {
            $crosssellProduct[] = $crsProduct->getSku();
        }
        return $crosssellProduct;
    }

    protected function getGroupedAssociatedProducts($gproduct) {
        $aproducts = array();
        $associatedProducts = $gproduct->getTypeInstance(true)->getAssociatedProducts($gproduct);
        foreach ($associatedProducts as $product) {
            $aproducts[$product->getSku()] = $product->getQty();
        }
        return $aproducts;
    }

    public function getAttributeSetName($attributeSetId) {
        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
        $attributeSetModel->load($attributeSetId);
        return $attributeSetModel->getAttributeSetName();
    }

    protected function getAttributes($product, $productbase) {
        $productData = $product->toArray();
        foreach($productData as $label => $value) {
            if($label == "url_key") {
                $productData[$label] = $product->getProductUrl();
            }
            if($value != $productbase->getData($label))
            {
                $productData[$label. '_overloaded'] = "true";
            } else {
                $productData[$label. '_overloaded'] = "false";
            }
            if($product->isGrouped() && $label == "sku") {
                $groupedParentId = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
                $_associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                $ogPrice = 0;
                foreach($_associatedProducts as $_associatedProduct) {
                    if($_associatedProduct->getPrice()) {
                        $ogPrice = $ogPrice + $_associatedProduct->getPrice();
                    }
                }
                
                $groupedParentIdbase = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($productbase->getId());
                $_associatedProductsbase = $productbase->getTypeInstance(true)->getAssociatedProducts($productbase);
                $ogPricebase = 0;
                foreach($_associatedProductsbase as $_associatedProductbase) {
                    if($_associatedProductbase->getPrice()) {
                        $ogPricebase = $ogPricebase + $_associatedProductbase->getPrice();
                    }
                }
                
                $productData['calculated_price'] =  $ogPrice;
                if($ogPrice != $ogPricebase){
                    $productData['calculated_price_overloaded'] = "true";
                } else {
                    $productData['calculated_price_overloaded'] = "false";
                }
            }
        }
        $icrcAttributeCodes = array();
        $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($productData['attribute_set_id'])
                ->setSortOrder()
                ->load();

        foreach ($groups as $group) {
            if ($group->getAttributeGroupName() == 'ICRC Attributes') {
                $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                        ->setAttributeGroupFilter($group->getId())
                        ->addVisibleFilter()
                        ->load();
        
                if ($attributes->getSize() > 0) {
                    foreach ($attributes->getItems() as $attribute) {

                        if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'boolean') {
                            $productData [$attribute->getAttributeCode()] = $product->getAttributeText($attribute->getAttributeCode());
                            if(!$attribute->getIsGlobal()) {
                                if($product->getData($attribute->getAttributeCode()) != $productbase->getData($attribute->getAttributeCode()))
                                {
                                    $productData[$attribute->getAttributeCode(). '_overloaded'] = "true";
                                } else {
                                    $productData[$attribute->getAttributeCode(). '_overloaded'] = "false";
                                }
                            }
                        }
                        if ($attribute->getFrontendInput() == 'multiselect') {
                            $value = $product->getAttributeText($attribute->getAttributeCode());
                            $productData [$attribute->getAttributeCode()] = (is_array($value)) ? implode(',', $value) : $value;
                            if(!$attribute->getIsGlobal()) {
                                if($product->getData($attribute->getAttributeCode()) != $productbase->getData($attribute->getAttributeCode()))
                                {
                                    $productData[$attribute->getAttributeCode(). '_overloaded'] = "true";
                                } else {
                                    $productData[$attribute->getAttributeCode(). '_overloaded'] = "false";
                                }
                            }
                        } else {
                            if($attribute->getAttributeCode() == "pdf") {
                                $productData [$attribute->getAttributeCode()] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/pdf/'.$product->getData($attribute->getAttributeCode());
                            } else {
                               $productData [$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
                            }
                            if(!$attribute->getIsGlobal()) {
                                if($product->getData($attribute->getAttributeCode()) != $productbase->getData($attribute->getAttributeCode()))
                                {
                                    $productData[$attribute->getAttributeCode(). '_overloaded'] = "true";
                                } else {
                                    $productData[$attribute->getAttributeCode(). '_overloaded'] = "false";
                                }
                            }
                        }

                        $icrcAttributeCodes[] = $attribute->getAttributeCode();
                    }
                }
            }
        }
        $productData['icrc_attribute_group'] = $icrcAttributeCodes;
        return $productData;
    }

    /**
     * 
     * @param type $setId
     * @return type
     */
    protected function getIcrcAttributes($setId) {

        $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

        $attributeCodes = array();
        foreach ($groups as $group) {
            if ($group->getAttributeGroupName() == 'ICRC Attributes') {
                $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                        ->setAttributeGroupFilter($group->getId())
                        ->addVisibleFilter()
                        ->checkConfigurableProducts()
                        ->load();
                if ($attributes->getSize() > 0) {
                    foreach ($attributes->getItems() as $attribute) {
                        /* @var $child Mage_Eav_Model_Entity_Attribute */
                        $attributeCodes[] = $attribute->getAttributeCode();
                    }
                }
            }
        }
        return $attributeCodes;
    }

    /**
     * 
     * @param type $associatedproducts
     * @param type $childs
     * @param type $type
     * @return type
     */
    protected function addproductChilds($associatedproducts, $childs, $type) {
        foreach ($associatedproducts as $sku => $attributes) {
            $bpr = $childs->addChild("Product");
            $sku = $bpr->addChild("SKU", $sku);
            if ($type == 'configurable') {
                $SproductData = $bpr->addChild("ProductData");
                foreach ($attributes as $index => $value) {
                    $SproductData->addChild($index, $value);
                }
            } elseif ($type == 'grouped') {
                $sku->addAttribute("default_qty", round($attributes, 0));
            }
        }
        return $childs;
    }

    /**
     * 
     * @return type
     */
    public function getSession() {
        return Mage::getSingleton('core/session');
    }
   
}

<?php

class Data_Icrc_Helper_Product extends Mage_Core_Helper_Abstract
{
  const LANG_ATTR = 'lang';
  const ALT_LANG_ATTR = 'languages_available';

  function getConfigurableAttributesHtml($product) {
    $html='';
    if ($product->isConfigurable()) {
      $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
      foreach ($attributes as $attribute) {
        $opts = $this->getOptions($attribute, $product);
        if (!array_key_exists('attribute', $opts)) continue;
        if ($attribute->getProductAttribute()->getAttributeCode() == self::LANG_ATTR) {
          if ($product->getLanguagesAvailable()) {
            $html .= $this->getLanguagesAvailable($product);
            continue;
          }
        }
        $values = array();
        foreach ($opts['attribute']['options'] as $val) $values[] = $val['label'];
        $html .= '<p>' . $this->__('%s: %s', $opts['attribute']['label'], implode($values, ' / ')) . '</p>';
      }
    }
    return $html;
  }

  function getAdditionalInfoHtml($product) {
    $html = '<p>' . $this->__('Type of product: ') . $this->__($this->getAttributeSetLabel($product)) . '</p>';
    if ($product->isConfigurable()) {
      $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
      foreach ($attributes as $attribute) {
        $opts = $this->getOptions($attribute, $product);
        if (!array_key_exists('attribute', $opts)) continue;
        if ($attribute->getProductAttribute()->getAttributeCode() == self::LANG_ATTR) {
          if ($product->getLanguagesAvailable()) {
            $html .= $this->getLanguagesAvailable($product);
            continue;
          }
        }
        $values = array();
        foreach ($opts['attribute']['options'] as $val) $values[] = $val['label'];
        $html .= '<p>' . $this->__('%s: %s', $opts['attribute']['label'], implode($values, ' / ')) . '</p>';
      }
    }
    return $html;
  }

  function getAttributeHtml($product, $attrCode) {
    if ($product->getData($attrCode)) {
      return '<p>' . $this->__('%s: ', $this->_getAttrLabel($attrCode)) . $product->getAttributeText($attrCode) . '<p>';
    }
    if ($product->isConfigurable()) {
      $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
      foreach ($attributes as $attribute) {
        $opts = $this->getOptions($attribute, $product);
        if (!array_key_exists('attribute', $opts)) continue;
        if ($attribute->getProductAttribute()->getAttributeCode() == $attrCode) {
          $values = array();
          foreach ($opts['attribute']['options'] as $val) $values[] = $val['label'];
          return '<p>' . $this->__('%s: %s', $this->_getAttrLabel($attrCode), implode($values, ' / ')) . '</p>';
        }
      }
    }
    return '';
  }

  private $_attrLbl;
  function attrCodeFromAttrLabel($lbl) {
    if (!isset($this->_attrLbl)) $this->_attrLbl = array();
    if (array_key_exists($lbl, $this->_attrLbl)) return $this->_attrLbl[$lbl];
    $attributes = Mage::getSingleton('eav/config')
      ->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection();
    foreach ($attributes as $attr) {
      $label = $attr->getLabel();
      if (!$label) $label = $attr->getFrontendLabel();
      $code = $attr->getAttributeCode();
      $this->_attrLbl[$label] = $code;
    }
    if (array_key_exists($lbl, $this->_attrLbl)) return $this->_attrLbl[$lbl];
    return $lbl;
  }

  function getCartInfoHtml($quote) {
    $product = Mage::getModel('catalog/product')->load($quote->getProductId());
    $html = '';
    if ($product->isConfigurable()) {
      $options = Mage::helper('catalog/product_configuration')->getConfigurableOptions($quote);
      foreach ($options as $opt) {
        $attrcode = $this->attrCodeFromAttrLabel($opt['label']);
        if ($attrcode != $opt['label']) $label = $this->_getAttrLabel($attrcode);
        else $label = $opt['label'];
        $html .= '<p>' . $this->__('%s: %s', $label, $opt['value']) . '</p>';
      }
    } elseif ($this->isSimple($product)) {
      $_attr = array('lang', 'film_system');
      foreach ($_attr as $attr) {
        if ($product->getData($attr))
          $html .= $this->getAttributeHtml($product, $attr);
      }
    }
    return $html;
  }

  function haveStock($quote) {
    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($quote->getProductId());
    if ($stock) {
      //Zend_debug::dump($stock->debug());
      if ($stock->getManageStock() == 0)
        return true;
      return $stock->getQty() > 0;
    }
    else return true;
  }

  /**
    * Be careful: this function doesn't handle currency rates ...
    */
  function getFormattedPriceHtml($product) {
	  $price = $product->getTierPrice(1);
	  $priceparts = explode(".", $price);
	  $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
    return '<span class="icrc-price">' . $priceparts[0] . '<sup>.' . substr($priceparts[1], 0, 2) . $currencySymbol . '</sup></span>';
  }

  function getPriceBlockHtml($product, $idSuffix = null) {
    if ($product->isGrouped())
      return $this->getGroupPriceBlockHtml($product, $idSuffix);
    $block = Mage::app()->getLayout()->createBlock('catalog/product_price');
    if ($block) {
      if ($idSuffix !== null) $block->setIdSuffix($idSuffix);
      return $block->setTemplate('catalog/product/price.phtml')->setProduct($product)->setInGrouped($product->isGrouped())->toHtml();
    }
    else
      return $this->getFormattedPriceHtml($product);
  }

  function getGroupPriceBlockHtml($product, $idSuffix = null) {
    $fake_block = Mage::app()->getLayout()->createBlock('data_icrc/grouped');
    return $fake_block->setProduct($product)->getPriceBlockHtml($idSuffix);
  /*
    $block = Mage::app()->getLayout()->createBlock('catalog/product_price');
    if ($block) {
      if ($idSuffix !== null) $block->setIdSuffix($idSuffix);
      return $block->setTemplate('catalog/product/price.phtml')->setProduct($product)->setInGrouped($product->isGrouped())->toHtml();
    }
    else
      return $this->getFormattedPriceHtml($product);
      */
  }

  function formatPriceHtml($price) {
	  return Mage::getModel('directory/currency')->load(Mage::app()->getStore()->getCurrentCurrencyCode())->formatPrecision($price, 2);
  }

  protected $attrSetIds = array();
  function getAttributeSetLabel($product) {
    $id = $product->getAttributeSetId();
    if (!array_key_exists($id, $this->attrSetIds))
      $this->attrSetIds[$id] = Mage::getModel('eav/entity_attribute_set')->load($id);
    return $this->attrSetIds[$id]->getAttributeSetName();
  }

  function getAttributeSetFrontLabel($product) {
    $tech = $this->getAttributeSetLabel($product);
    switch ($tech) {
      case 'donation': return 'Donation';
      case 'ebook': return 'e-Book';
      case 'exhibition': return 'Exhibition';
      case 'films': return 'Film';
      case 'gift': return 'Gift';
      case 'publications': return 'Publication';
    }
    return $tech;
  }

  function getOptions($attribute, $product, $store = null, $checkStock = false) {
    if ($store == null) $store = Mage::app()->getStore();
    $currentProduct = $product;

    $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
    if ($preconfiguredFlag) {
        $preconfiguredValues = $currentProduct->getPreconfiguredValues();
    }

    $productAttribute = $attribute->getProductAttribute();
    $attributeId = $productAttribute->getId();
    $info = array(
       'id'        => $productAttribute->getId(),
       'code'      => $productAttribute->getAttributeCode(),
       'label'     => $attribute->getLabel(),
       'options'   => array()
    );
    if ($attribute->getStoreLabel()) $info['label'] = $attribute->getStoreLabel();
    elseif ($attribute->getFrontendLabel()) $info['label'] = $attribute->getFrontendLabel();
    $prices = $attribute->getPrices();

    if ($checkStock) {
      $children = array();
      $_configurable = $currentProduct->getTypeInstance()->getUsedProducts();//var_dump($productAttribute->getData());
      foreach ($_configurable as $child) {
        $key = $child->getData($productAttribute->getAttributeCode());
        $children[$key] = $child->isSaleable();
      }
    }

    if (is_array($prices)) {
        foreach ($prices as $value) {
            if ($checkStock) {
                if (!$children[$value['value_index']])
                    continue; // Skip product if not saleable
            }
            $info['options'][] = array(
                'id'        => $value['value_index'],
                'label'     => $value['label'],
            );
        }
    }

    $config = array(
        'attribute'         => $info,
        'productId'         => $currentProduct->getId()
    );

    // Add attribute default value (if set)
    if ($preconfiguredFlag) {
        $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
        if ($configValue) {
            $config['defaultValues'] = $configValue;
        }
    }

    return $config;
  }

  public function getDiscountInfoHtml($_item) {
    if ($_item->getDiscountAmount() && $_item->getCalculationPrice()) {
      $total_discount = $_item->getDiscountAmount();
      $single_discount = $total_discount / $_item->getQty();
      $perc_discount = ($single_discount / $_item->getCalculationPrice()) * 100;
      $final_price = $_item->getRowTotal() - $total_discount;
      return '<span class="icrc-discount">' . $this->__('Discount: ') . round($perc_discount) . ' %</span>' .
        '<br /><span class="icrc-price-discounted">' . Mage::helper('checkout')->formatPrice($final_price) . '</span>';
    }
    return '';
  }

  public function isPublication($_item) {
    return $this->getAttributeSetLabel($_item) == 'publications';
  }

  public function isEBook($_item) {
    return $this->getAttributeSetLabel($_item) == 'ebook';
  }

  public function isGift($_item) {
    return $this->getAttributeSetLabel($_item) == 'gift';
  }

  public function isDonation($_item) {
    return $this->getAttributeSetLabel($_item) == 'donation';
  }

  public function isFilm($_item) {
    return $this->getAttributeSetLabel($_item) == 'films';
  }

  public function isExhibition($_item) {
    return $this->getAttributeSetLabel($_item) == 'exhibition';
  }

  public function isSimple($_item) {
    return $_item->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
  }

  private $_altLangOptions;
  private $_altLangLabel;
  protected function _getAltLangOptions() {
    if (isset($this->_altLangOptions)) return $this->_altLangOptions;
    $attributeId = Mage::getResourceModel('eav/entity_attribute')
      ->getIdByCode('catalog_product', self::ALT_LANG_ATTR);
    $attribute = Mage::getModel('catalog/resource_eav_attribute')
      ->load($attributeId);
Data_Icrc_Helper_Debug::dump($attribute);
    $this->_altLangLabel = $attribute->getFrontendLabel();
    if ($attribute->getStoreLabel())
      $this->_altLangLabel = $attribute->getStoreLabel();
    $this->_altLangOptions = $attribute->getSource()->getAllOptions();
    return $this->_altLangOptions;
  }

  protected function _getAltLangLabel() {
    if (!isset($this->_altLangLabel)) $this->_getAltLangOptions();
    return $this->_altLangLabel;
  }

  private $_attrLabel;
  protected function _getAttrLabel($code) {
    if (!isset($this->_attrLabel))
      $this->_attrLabel = array();
    if (!array_key_exists($code, $this->_attrLabel)) {
      $attributeId = Mage::getResourceModel('eav/entity_attribute')
        ->getIdByCode('catalog_product', $code);
      $attribute = Mage::getModel('catalog/resource_eav_attribute')
        ->load($attributeId);
      $this->_attrLabel[$code] = $attribute->getFrontendLabel();
      if ($attribute->getStoreLabel())
        $this->_attrLabel[$code] = $attribute->getStoreLabel();
    }
    return $this->_attrLabel[$code];
  }

  public function getAttributeLabelFromLabel($label) {
    $attrcode = $this->attrCodeFromAttrLabel($label);
    if ($attrcode != $label) {
      return $this->_getAttrLabel($attrcode);
    }
    return $label;
  }

  public function getAttributeLabel(Mage_Catalog_Model_Product_Type_Configurable_Attribute $attr) {
    $attribute = $attr->getProductAttribute();
    $code = $attribute->getAttributeCode();
    if (!isset($this->_attrLabel))
      $this->_attrLabel = array();
    if (!array_key_exists($code, $this->_attrLabel)) {
      $this->_attrLabel[$code] = $attribute->getFrontendLabel();
      if ($attribute->getStoreLabel())
        $this->_attrLabel[$code] = $attribute->getStoreLabel();
    }
    return $this->_attrLabel[$code];
  }
  
  public function getSimpleLanguage($product) {
    return $this->getSimpleGroupedLanguage($product);
  }
  
  public function getGroupedLanguage($product) {
    return $this->getSimpleGroupedLanguage($product);
  }
  
  public function getSimpleGroupedLanguage($product) {
    if (!$product->getLang())
      return '';
    if ($product->getLangValue())
      $l = $product->getLangValue();
    else {
      $l = $product->getResource()->getAttribute('lang')->getFrontend()->getValue($product);
    }
    return '<p class="simple-language">' . $this->__('Language: %s', $l) . '</p>';
  }
  
  public function getGroupedAvailableLanguage($product) {
    if (!$product->getLanguagesAvailable())
      return '';
    $available = explode(',', $product->getLanguagesAvailable());
    $res = array();
    foreach ($this->_getAltLangOptions() as $o) {
      if (in_array($o['value'], $available)) {
        $res[] = $o['label'];
      }
    }
    if (count($res))
      return '<p>' . $this->__('Available Languages: %s', implode(' / ', $res)) . '</p>';
    else
      return '';
  }

  public function getLanguagesAvailable($product) {
    if (!$product->isConfigurable())
      return '';
    if (!$product->getLanguagesAvailable()) {
      if (!$product->isConfigurable() && $product->getLangValue())
        return '<p>' . $this->__('Language: %s', $product->getLangValue()) . '<p>';
      return $this->getAttributeHtml($product, self::LANG_ATTR);
    }
    if ($product->canConfigure()) {
      $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
      foreach ($attributes as $attribute) {
        if ($attribute->getProductAttribute()->getAttributeCode() == self::LANG_ATTR) {
          $a = $this->getOptions($attribute, $product);
          if (array_key_exists('attribute', $a) && array_key_exists('options', $a['attribute']))
            $opts = $a['attribute']['options'];
          break;
        }
      }
    }
    $available = explode(',', $product->getLanguagesAvailable());
    $res = array();
    $js = '';
    foreach ($this->_getAltLangOptions() as $o) {
      if (in_array($o['value'], $available)) {
        $v = $o['label'];
        if (isset($opts)) {
          $found = false;
          foreach ($opts as $so) {
            if ($so['label'] == $o['label']) {
              $found = true;
              break;
            }
          }
          if (!$found) {
            $v .= '<em class="remark">*</em>';
            $js = '<script type="text/javascript">
//<![CDATA[
document.observe("dom:loaded", function() {
    $("language_available_message_sidebar").show();
  });
//]]></script>';
          }
        }
        $res[] = $v;
      }
    }
    return '<p>' . $this->_getAltLangLabel() . $this->__(': ') . implode(' / ', $res) . '</p>' . $js;
  }

  public function getFixedWitdhThumbnail($product, $size) {
    list ($URL, $margintop, $marginright, $marginbottom, $marginleft, $width, $height) =
      Mage::helper('data_icrc')->getImageRatioWithInfo($product, $size);
    if ($width != $size) {
      $h = $size / $width * $height;
      list ($URL, $margintop, $marginright, $marginbottom, $marginleft, $width, $height) =
        Mage::helper('data_icrc')->getImageRatioWithInfo($product, $h);
    }
    return array($width, $height, $URL);
  }

    public function havePdfProduct($ebookLink) {
        if ($ebookLink) {
            if ($ebookLink->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
              return null; // there is an ebook product, but it's disabled
            return Mage::getBaseUrl().$ebookLink->getUrlPath();
            foreach ($categories as $category) // Pick the first category
              return $ebookLink->getUrlPath(Mage::getModel('catalog/category')->load($category->getId()));
            return $ebookLink->getProductUrl();
        }
        return null;
    }
  
    public function getEbook($sku)
    {
        return Mage::getModel('catalog/product')->loadByAttribute('sku', $sku . '-ebook');
    }

  public function isSaleable($product) {
    Mage::unregister('non_saleable_message');
    if ($this->isEBook($product))
      return true;
    $saleable = $product->isSaleable();
    if (!$saleable)
      return $saleable;
    if (Mage::app()->getStore()->getCode() == 'internal') {
      if ($this->isGift($product)) {
        Mage::register('non_saleable_message', $this->__('Cannot buy gifts from internal store'));
        return false;
      }
    }
    return $saleable;
  }

  public function getStaticFields() {
    $res = Mage::getModel('catalog/product')->getResource();
    $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
    $table = $res->getEntityTable();
    return $adapter->describeTable($table);
  }
  
  public function getProductUrl($product) {
    switch ($product->getVisibility()) {
      case Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG:
      case Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH:
        return $product->getProductUrl();
    }
    Data_Icrc_Helper_Debug::dump($product->getData());
    if ($product->getTypeId() == "simple") {
      $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
      if (isset($parentIds[0]))
        return Mage::getModel('catalog/product')->load($parentIds[0])->getProductUrl();
    }
    return $product->getProductUrl();
  }
  
  // Handles simple products from grouped products: get image & url from configurable
  public function overrideProduct(Mage_Checkout_Block_Cart_Item_Renderer $renderer) {
    $_item = $renderer->getItem();
    $renderer->overrideProductThumbnail(null);
    $renderer->overrideProductUrl(null);
    if (Mage::helper('data_icrc/product')->isSimple($_item->getProduct()) &&
        in_array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE, $_item->getProduct()->getVisibleInCatalogStatuses())) {
      $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($_item->getProduct()->getId());
      if (isset($parentIds[0])) {
        $_prd = Mage::getModel('catalog/product')->load($parentIds[0]);
        $renderer->overrideProductThumbnail(Mage::helper('catalog/image')->init($_prd, 'thumbnail'));
        $renderer->overrideProductUrl($_prd->getProductUrl());
      }
    }
  }
    /**
     * 
     * @param type $product
     * @return boolean
     */
    public function getOptionsConfigurable($product)
    {
        if(!$product->isConfigurable()) {
            return false;
        } else {
            return Mage::getModel('catalog/product_type_configurable')
                ->getUsedProductCollection($product)
                ->addFilterByRequiredOptions()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
        }
    }
    
    public function getOptionsConfigurableOfEbook($product)
    {
        if(!$this->isEBook($product)) {
            $ebookLink = $this->getEbook($product->getSku());
            return $this->getOptionsConfigurable($ebookLink);
        }
        return false;
    }
    
    public function getLangAvailable($product)
    {
        if (!$product->isConfigurable())
        return '';
      if (!$product->getData('languages_available')) {
        if (!$product->isConfigurable() && $product->getLangValue())
          return '<p>' . $this->__('Language: %s', $product->getLangValue()) . '<p>';
        return $this->getAttributeHtml($product, self::LANG_ATTR);
      }
      if ($product->canConfigure()) {
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
        foreach ($attributes as $attribute) {
          if ($attribute->getProductAttribute()->getAttributeCode() == self::LANG_ATTR) {
            $a = $this->getOptions($attribute, $product);
            if (array_key_exists('attribute', $a) && array_key_exists('options', $a['attribute']))
              $opts = $a['attribute']['options'];
            break;
          }
        }
      }
      $available = explode(',', $product->getData('languages_available'));
      $res = array();
      foreach ($this->_getAltLangOptions() as $o) {
        if (in_array($o['value'], $available)) {
          $v = $o['label'];
          if (isset($opts)) {
            $found = false;
            foreach ($opts as $so) {
              if ($so['label'] == $o['label']) {
                $found = true;
                break;
              }
            }
            if (!$found) {
                $res[] = $o['label'];
            }
          }
        }
      }
      return $res;
    }

    function fullQuoteEscape($str) {
        $decoded = htmlspecialchars_decode($str, ENT_QUOTES|ENT_XHTML);
        $recoded = htmlspecialchars($decoded, ENT_COMPTA|ENT_XHTML);
        return Mage::Helper('core')->jsQuoteEscape($recoded);
    }
}


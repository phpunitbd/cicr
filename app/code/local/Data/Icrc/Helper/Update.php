<?php

class Data_Icrc_Helper_Update extends Mage_Core_Helper_Abstract
{
  public $TRANSLATE_LANG = array(
    'English' => 'anglais',
    'French' => 'français',
    'Spanish' => 'espagnol',
    'Arabic' => 'arabe',
    'Russian' => 'russe',
    'Chinese' => 'chinois',
    'Portuguese' => 'portugais'
    );

  public function __construct() {
    $this->en = Mage::getModel('core/store')->load('default', 'code')->getId();
    $this->fr = Mage::getModel('core/store')->load('fr', 'code')->getId();
    $this->int = Mage::getModel('core/store')->load('internal', 'code')->getId();
    $this->public = Mage::getModel('core/website')->load('base', 'code')->getId();
    $this->internal = Mage::getModel('core/website')->load('internal', 'code')->getId();
  }

  /**
    * @returns list($en, $fr, $int) store ids
    */
  public function getStoreIds() {
    return array($this->en, $this->fr, $this->int);
  }

  /**
    * @returns list($public, $internal) website ids
    */
  public function getWebsiteIds() {
    return array($this->public, $this->internal);
  }

  public function doTranslateAttributeOptions($attribute, $translation, $store_id = null) {
    $opts = Mage::getModel('eav/entity_attribute_option')->getCollection()
      ->setAttributeFilter($attribute->getId())->setStoreFilter(0);
    $table = $opts->getResource()->getTable('attribute_option_value');

    $adapter = $opts->getConnection();
    if ($store_id === null) $store_id = $this->fr;

    foreach ($opts->toOptionArray() as $o) {
      $r = $adapter->fetchAll("select 1 from `$table` where option_id = $o[value] and store_id = $store_id");
      if (count($r))
        $rows = $adapter->update($table, array('value' => $translation[$o['label']]), 
                                'option_id = '.$o['value'].' and store_id = '.$this->fr);
      else
        $adapter->insert($table, array('option_id' => $o['value'], 
                                       'store_id' => $this->fr, 
                                       'value' => $translation[$o['label']]));
    }
  }

  public function createCmsPage($title, $store, $id, $cnt = null) {
    try {
      if (is_array($store)) $stores = $store;
      else $stores = array($store);
      $cmsPage = array(
              'title' => $title,
              'identifier' => $id,
              'content' => $cnt ? $cnt : '<h1>Content for '.$id.'<p class="accroche">Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
  Cras porta, leo ut porttitor interdum, odio tellus sodales erat, ac laoreet odio nulla a felis.</p>
  <h2>Title 1</h2>
  <p>Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. 
  In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lor
  em. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui 
  id risus. Curabitur nec sodales augue.</p>
  <h2>Title 1</h2>
  <p>Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.</p>
  <h2>Title 1</h2>
  <p>Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.</p>',
              'is_active' => 1,
              'sort_order' => 0,
              'stores' => $stores,
              'root_template' => 'one_column'
              );
   
      Mage::getModel('cms/page')->setData($cmsPage)->save();
    }
    catch (Mage_Core_Exception $e) {
      // ignore
    }
  }

  public function updateCmsPage($id, $store, $cnt) {
    $collection = Mage::getModel('cms/page')->getCollection()
              ->addStoreFilter($store)// You have to provide a store id or Mage_Core_Model_Store Object @see class Mage_Cms_Model_Mysql4_Page_Collection
              ->addFieldToFilter('is_active', 1);
    foreach ($collection as $page) {
      if ($page->getIdentifier() == $id) {
        //var_dump($page);
        Mage::getModel('cms/page')->load($page->getId())->setContent($cnt)->save();
      }
    }
  }

  private $currenciesLoaded = false;
  public function loadCurrencies() {
    if ($this->currenciesLoaded)
      return;
    $service = Mage::getStoreConfig('currency/import/service');
    if (!$service) $service = 'webservicex';
    if ($service) {
      try {
        $importModel = Mage::getModel(Mage::getConfig()->getNode('global/currency/import/services/' . $service . '/model')->asArray());
        $rates = $importModel->fetchRates();
        $errors = $importModel->getMessages();
        if (sizeof($errors) == 0) {
          Mage::getModel('directory/currency')->saveRates($rates);
          $this->currenciesLoaded = true;
        }
      } catch (Exception $e) {
        error_log($e->getMessage());
        Data_Icrc_Helper_Debug::msg($e->getTraceAsString());
      }
    }
  }
  
  public function recordAttr($setup, $attributes, $set) {
    foreach ($attributes as $attl => $att) {
      if ($att[1] == null) {
        if ($set) {
          $attributeId = $setup->getAttribute('catalog_product', $att[0], 'attribute_id');
          //error_log("addAttributeToSet: $set ($attl:$attributeId)");
          $setup->addAttributeToSet('catalog_product', $set, 'ICRC Attributes', $attributeId);
        }
        continue;
      }
      //error_log('add attribute: '.$attl);
      $info = array(
        'type' => $att[2] ? $att[2] : 'varchar',
        'label' => $attl,
        'input' => $att[1],
        'required' => false,
        'user_defined' => true,
        'global' => true
      );
      if (!$set) $info['group'] = 'ICRC Attributes';
      $setup->addAttribute('catalog_product', $att[0], $info);
      $attributeId = $setup->getAttribute('catalog_product', $att[0], 'attribute_id');
      //error_log("*** got attributeId: $attributeId");
      if ($att[3]) {
        //error_log('set attribute visible: '.$attl);
        $setup->updateAttribute('catalog_product', $att[0], 
                                array('is_visible' => true, 'is_visible_on_front' => true, 'is_searchable' => true));
      }
      if (array_key_exists('add', $att) && is_array($att['add'])) {
        //error_log('additional');
        $opts = array();
        foreach ($att['add'] as $o => $v) {
          if (is_int($o)) $opts[$v] = true;
          else $opts[$o] = $v;
        }
        $setup->updateAttribute('catalog_product', $att[0], $opts);
      }
      if (array_key_exists('values', $att) && is_array($att['values'])) {
        //error_log('set values');
        $option = array();
        $option['attribute_id'] = $attributeId;
        foreach ($att['values'] as $label)
          $option['values'][] = $label;
        $setup->addAttributeOption($option);
      }
      if ($set) {
        //error_log("addAttributeToSet: $set ($attl)");
        $setup->addAttributeToSet('catalog_product', $set, 'ICRC Attributes', $attributeId);
        if (!$att[3]) $setup->updateAttribute('catalog_product', $att[0], array('is_configurable' => false));
      }
    }
  }
  
  public function getAttributeSetId($attributeSetName) {
    $attribute_set = Mage::getModel("eav/entity_attribute_set")->getCollection();
    return $attribute_set->addFieldToFilter("attribute_set_name", $attributeSetName)->getFirstItem()->getAttributeSetId();
  }
}

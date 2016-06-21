<?php

$this->startSetup();

function createPage_48($title, $store, $id, $cnt = null)
{
  try {
    $cmsPage = array(
            'title' => $title,
            'identifier' => $id,
            'content' => $cnt ? $cnt : '<h1>Content for '.$id.'<p class="accroche">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras porta, leo ut porttitor interdum, odio tellus sodales erat, ac laoreet odio nulla a felis.</p>
<h2>Title 1</h2>
<p>Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.</p>
<h2>Title 1</h2>
<p>Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.</p>
<h2>Title 1</h2>
<p>Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.</p>',
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array($store),
            'root_template' => 'one_column'
            );
 
    Mage::getModel('cms/page')->setData($cmsPage)->save();
  }
  catch (Mage_Core_Exception $e) {
    // ignore
  }
}


list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('catalog/frontend/list_mode', 'list', 'default', 0);

foreach (array('image', 'production_cost', 'product_type') as $attr) {
  $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attr);
  $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
  $attribute->setUsedInProductListing(1)->save();
}

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'theme');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$labels = array(0 => 'Subcategory', $en => 'Subcategory', $fr => 'Sous-catégorie', $int => 'Subcategory');
$attribute->setStoreLabels($labels)->save();

createPage_48('Exchange', array($en, $int), 'exchange');
createPage_48('Échange', $fr, 'exchange');
createPage_48('Privacy Policy', array($en, $int), 'privacy-policy');
createPage_48('Politique de confidentialité', $fr, 'privacy-policy');


$this->endSetup();


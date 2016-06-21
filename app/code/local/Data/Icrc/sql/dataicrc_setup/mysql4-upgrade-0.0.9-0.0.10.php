<?php

$this->startSetup();

function createPage2($title, $store, $id, $cnt = null, $head = null)
{
  try {
    $cmsPage = array(
            'title' => $title,
            'identifier' => $id,
            'content' => $cnt ? $cnt : '<h1>Content for '.$id.'</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras porta, leo ut porttitor interdum, odio tellus sodales erat, ac laoreet odio nulla a felis. Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.</p>',
            'meta_description' => $head ? $head : "Description for $id : Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras porta, leo ut porttitor interdum, odio tellus sodales erat, ac laoreet odio nulla a felis. Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.",
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array($store),
            'root_template' => 'two_columns_right'
            );
 
    Mage::getModel('cms/page')->setData($cmsPage)->save();
  }
  catch (Mage_Core_Exception $e) {
    // ignore
  }
}
$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

createPage2('Newsletter', $en, 'newsletter');
createPage2('Newsletter', $fr, 'newsletter');

/* Add default description for pricing policy */
/*
$c = Mage::getModel('cms/page')->getCollection()->addFilter('identifier', 'pricing-policy');
foreach ($c as $p) {
  $p->load();
  if ($p->getMetaDescription() == '')
    $p->setMetaDescription('Description for ' . $p->getTitle() . ' -- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras porta, leo ut porttitor interdum, odio tellus sodales erat, ac laoreet odio nulla a felis. Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.')->save();
}
*/

$this->endSetup();


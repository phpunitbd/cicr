<?php

$this->startSetup();

function createPage_7_8($title, $store, $id, $cnt = null)
{
  try {
    $cmsPage = array(
            'title' => $title,
            'identifier' => $id,
            'content' => $cnt ? $cnt : '<h1>Content for '.$id.'</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras porta, leo ut porttitor interdum, odio tellus sodales erat, ac laoreet odio nulla a felis. Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.</p>',
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

createPage_7_8('Pricing Policy', $en, 'pricing-policy');
createPage_7_8('Politique tarifaire', $fr, 'pricing-policy');
createPage_7_8('Legal Notices', $en, 'legal-notices');
createPage_7_8('Mentions légales', $fr, 'legal-notices');
createPage_7_8('Shipping', $en, 'shipping-policy');
createPage_7_8('Livraisons', $fr, 'shipping-policy');
createPage_7_8('Payment Security', $en, 'payment-policy-security');
createPage_7_8('Sécurité des paiements', $fr, 'payment-policy-security');

$this->endSetup();


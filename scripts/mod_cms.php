<?php
include_once('app/Mage.php');
Mage::app();

$c = Mage::getModel('cms/page')->getCollection()->addFilter('identifier', 'pricing-policy');
foreach ($c as $p) {
  $p->load();
  if ($p->getMetaDescription() == '')
    $p->setMetaDescription('Description for ' . $p->getTitle() . ' -- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras porta, leo ut porttitor interdum, odio tellus sodales erat, ac laoreet odio nulla a felis. Quisque quis sapien id nisl faucibus placerat. Suspendisse potenti. Duis dictum iaculis justo, id adipiscing massa scelerisque sit amet. In sagittis est nulla, quis tempor lacus. Nulla scelerisque congue aliquet. Ut gravida eros dapibus quam sollicitudin fringilla sed eget lorem. Nulla molestie scelerisque dignissim. Vestibulum interdum, magna id feugiat vulputate, justo justo vulputate odio, et tristique est dui id risus. Curabitur nec sodales augue.')->save();
}




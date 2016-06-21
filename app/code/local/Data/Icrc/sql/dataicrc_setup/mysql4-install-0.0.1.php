<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

error_log('add attribute');

$setup->addAttribute('catalog_product', 'recent_import',
  array(
    'type' => 'int',
    'label' => 'Recently imported',
    'input' => 'boolean',
    'required' => true,
    'user_defined' => false,
    'default' => '0',
    'visible_on_front' => false,
    'global' => true
));

error_log('end');

$setup->endSetup();


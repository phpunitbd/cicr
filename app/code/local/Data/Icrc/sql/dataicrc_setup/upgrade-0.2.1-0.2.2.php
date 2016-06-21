<?php

$this->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('customer_address', 'icrc_type', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Icrc Address Type',
    'global' => 1,
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));
$setup->addAttribute('customer_address', 'icrc_unit', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Icrc Unit or Delegation Code',
    'global' => 1,
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));

$this->endSetup();


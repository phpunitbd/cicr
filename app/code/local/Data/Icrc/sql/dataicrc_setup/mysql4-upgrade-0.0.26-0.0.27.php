<?php

$installer = $this;
$installer->startSetup();

$collection = Mage::getModel('salesrule/rule')->getCollection();

foreach ($collection as $rule) {
  if (preg_match('/^Discount [0-9]+-(([0-9]+)|infinite)$/', $rule->getName()))
    $rule->setStopRulesProcessing(0)->save();
}

$installer->endSetup();


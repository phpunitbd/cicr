<?php

$installer = $this;
$installer->startSetup();

$shoppingCartPriceRule = Mage::getModel('salesrule/rule');
$shoppingCartPriceRule
  ->setName('Special customer Types')
  ->setDescription('20% Discount for national societies and federations')
  ->setIsActive(1)
  ->setWebsiteIds(array(1))
  ->setCustomerGroupIds(array(0, 1, 2, 3))
  ->setFromDate('')
  ->setToDate('')
  ->setSortOrder('')
  ->setSimpleAction('by_percent')
  ->setDiscountAmount(20)
  ->setSortOrder(10)
  ->setStopRulesProcessing(1);
$fede = Mage::getModel('salesrule/rule_condition_product')
  ->setType('data_icrc/condition_clientType')
  ->setAttribute('clientType')
  ->setOperator('==')
  ->setValue('federation');
$soc = Mage::getModel('salesrule/rule_condition_product')
  ->setType('data_icrc/condition_clientType')
  ->setAttribute('clientType')
  ->setOperator('==')
  ->setValue('society');
$shoppingCartPriceRule->getConditions()->setAggregator('any');
$shoppingCartPriceRule->getConditions()->addCondition($fede);
$shoppingCartPriceRule->getConditions()->addCondition($soc);
$shoppingCartPriceRule->save();

$icrc = Mage::helper('data_icrc')->getICRCGroup();
if ($icrc === null) {
  $customer_group = Mage::getModel('customer/group');
  $customer_group->setCode('ICRC')->setTaxClassId(3)->save();
  $icrc = $customer_group->getId();
}

$shoppingCartPriceRule = Mage::getModel('salesrule/rule');
$shoppingCartPriceRule
  ->setName('ICRC collaborators')
  ->setDescription('20% Discount for ICRC staff')
  ->setIsActive(1)
  ->setWebsiteIds(array(1))
  ->setCustomerGroupIds(array($icrc))
  ->setFromDate('')
  ->setToDate('')
  ->setSortOrder('')
  ->setSimpleAction('by_percent')
  ->setDiscountAmount(20)
  ->setSortOrder(10)
  ->setStopRulesProcessing(1);
$shoppingCartPriceRule->save();

// Rebuild prices (index 2)
try {
  Mage::getResourceModel('catalog/product_flat_indexer')->rebuild();
  $process = Mage::getModel('index/process')->load(2); $process->reindexAll();
} catch (Exception $e) {
  // DO NOTHING
}

$installer->endSetup();


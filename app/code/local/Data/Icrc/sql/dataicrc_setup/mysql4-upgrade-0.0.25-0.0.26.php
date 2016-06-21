<?php

$installer = $this;
$installer->startSetup();

$previous = array('Special customer Types', 'ICRC collaborators');

$categories = Mage::getModel('catalog/category')->getCollection();
$categories->addAttributeToFilter('name', 'Donations');
foreach ($categories as $cat) {
  $cat_id = $cat->getId();
}
if (!isset($cat_id)) throw new Exception("Cannot find Donations category");

$collection = Mage::getModel('salesrule/rule')->getCollection();

$don = Mage::getModel('salesrule/rule_condition_product')
  ->setType('salesrule/rule_condition_product')
  ->setAttribute('category_ids')
  ->setOperator('!=')
  ->setValue($cat_id);

foreach ($collection as $rule) {
  if (!in_array($rule->getName(), $previous))
    continue;
  $found = false;
  foreach ($rule->getActions()->getConditions() as $cond) {
    if ($cond->getType() != $don->getType()) continue;
    if ($cond->getAttribute() != $don->getAttribute()) continue;
    if ($cond->getOperator() != $don->getOperator()) continue;
    if ($cond->getValue() != $don->getValue()) continue;
    $found = true;
    break;
  }
  if (!$found) {
    $rule->getActions()->addCondition($don);
    $rule->setSortOrder(10)->save();
  }
}

function create_rule_discount_on_qty($min, $max, $discount, $don) {
  $shoppingCartPriceRule = Mage::getModel('salesrule/rule');
  $maxl = $max === null ? 'infinite' : $max;
  $shoppingCartPriceRule
    ->setName("Discount ${min}-${maxl}")
    ->setDescription("${discount}% discount for qty between $min and $maxl")
    ->setIsActive(1)
    ->setWebsiteIds(array(1))
    ->setCustomerGroupIds(array(0, 1, 2, 3))
    ->setFromDate('')
    ->setToDate('')
    ->setSortOrder('')
    ->setSimpleAction('by_percent')
    ->setDiscountAmount($discount)
    ->setSortOrder(5)
    ->setStopRulesProcessing(0);
  $cond_min = Mage::getModel('salesrule/rule_condition_product')
    ->setType('salesrule/rule_condition_product')
    ->setAttribute('quote_item_row_total')
    ->setOperator('>=')
    ->setValue($min);
  if ($max !== null)
    $cond_max = Mage::getModel('salesrule/rule_condition_product')
      ->setType('salesrule/rule_condition_product')
      ->setAttribute('quote_item_row_total')
      ->setOperator('<=')
      ->setValue($max);
  $shoppingCartPriceRule->getActions()->addCondition($cond_min);
  if ($max !== null)
    $shoppingCartPriceRule->getActions()->addCondition($cond_max);
  $shoppingCartPriceRule->getActions()->addCondition($don);
  $shoppingCartPriceRule->save();
}
create_rule_discount_on_qty(10, 19, 10, $don);
create_rule_discount_on_qty(20, 49, 15, $don);
create_rule_discount_on_qty(50, 99, 20, $don);
create_rule_discount_on_qty(100, 499, 25, $don);
create_rule_discount_on_qty(500, 999, 30, $don);
create_rule_discount_on_qty(1000, 4999, 40, $don);
create_rule_discount_on_qty(5000, 9999, 50, $don);
create_rule_discount_on_qty(10000, null, 60, $don);

$installer->endSetup();


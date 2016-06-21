<?php

$installer = $this;
$installer->startSetup();

$tax_classes = Mage::getModel('tax/class')->getCollection();
foreach ($tax_classes as $tax_class) {
  if ($tax_class->getClassName() == 'Retail Customer' && $tax_class->getClassType() == 'CUSTOMER') {
    $cust_id = $tax_class->getClassId();
  }
  if ($tax_class->getClassName() == 'Taxable Goods' && $tax_class->getClassType() == 'PRODUCT') {
    $product_full = $tax_class->getClassId();
  }
}

if (!isset($cust_id)) {
  throw new Exception('Cannot find Retail Customer tax class');
}
if (!isset($product_full)) {
  throw new Exception('Cannot find Taxable Goods tax class');
}

$tax_rate_redu = Mage::getModel('tax/calculation_rate');
$tax_rate_redu->setTaxCountryId('CH')
              ->setTaxRegionId(0)
              ->setTaxPostcode('*')
              ->setCode('Swiss-Reduced')
              ->setRate(2.5)
              ->save();
$tax_rate_full = Mage::getModel('tax/calculation_rate');
$tax_rate_full->setTaxCountryId('CH')
              ->setTaxRegionId(0)
              ->setTaxPostcode('*')
              ->setCode('Swiss-General')
              ->setRate(8.0)
              ->save();

$product_redu = Mage::getModel('tax/class');
$product_redu->setClassType('PRODUCT')
             ->setClassName('Reduced Taxable Goods')
             ->save();

/*
 * for some reason, this isn't working in INSTALL mode
 * then we use a direct insert into database
 *
$tax_rule_redu = Mage::getModel('tax/calculation_rule');
$tax_rule_redu->setCode('Swiss-Reduced')->setPriority(0)->setPosition(0)->save();
$tax_rule_full = Mage::getModel('tax/calculation_rule');
$tax_rule_full->setCode('Swiss-General')->save();
*/
$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$installer->run("
  insert into tax_calculation_rule (code, priority, position)
  select 'Swiss-Reduced', 0, 0 union all select 'Swiss-General', 0, 0
");
$tax_rules = Mage::getModel('tax/calculation_rule')->getCollection();
foreach ($tax_rules as $tax_rule) {
  if ($tax_rule->getCode() == 'Swiss-Reduced') $tax_rule_redu = $tax_rule;
  if ($tax_rule->getCode() == 'Swiss-General') $tax_rule_full = $tax_rule;
}

$tax_calc_redu = Mage::getModel('tax/calculation');
$tax_calc_redu->setTaxCalculationRateId($tax_rate_redu->getId())
              ->setTaxCalculationRuleId($tax_rule_redu->getId())
              ->setCustomerTaxClassId($cust_id)
              ->setProductTaxClassId($product_redu->getId())
              ->save();
$tax_calc_full = Mage::getModel('tax/calculation');
$tax_calc_full->setTaxCalculationRateId($tax_rate_full->getId())
              ->setTaxCalculationRuleId($tax_rule_full->getId())
              ->setCustomerTaxClassId($cust_id)
              ->setProductTaxClassId($product_full)
              ->save();

$installer->endSetup();


<?php

$installer = $this;
$installer->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('tax/calculation/apply_after_discount', '1', 'default', 0);
$conf->saveConfig('icrc/web/cart_image', 'images/cicr-small-cart.png', 'stores', $fr);
$conf->saveConfig('icrc/web/big_cart_image', 'images/cicr-cart.png', 'stores', $fr);
$conf->saveConfig('payment/saferpay_creditcard/title', 'Carte de crédit par Saferpay', 'stores', $fr);
$conf->saveConfig('payment/free/title', 'Commande gratuite', 'stores', $fr);
$conf->saveConfig('payment/free/title', 'Free Order', 'default', 0);

$countries = array('AF','AO','AM','BD','BZ','BJ','BT','BO','BF','BI','KH','CM','CV','CF','TD','KM','CD','CG','CI','DJ','EG','SV','ER','ET','FJ','GM','GE','GH','GT','GN','GW','GY','HT','HN','IN','ID','IQ','KE','KI','KP','KG','LA','LS','LR','MG','MW','ML','MH','MR','FM','MD','MN','MA','MZ','MM','NP','NI','NE','NG','PK','PG','PY','PH','RW','WS','ST','SN','SL','SB','SO','SS','LK','SD','SZ','SY','TJ','TZ','TL','TG','TO','TM','TV','UG','UA','UZ','VU','VN','YE','ZM','ZW');
$shoppingCartPriceRule = Mage::getModel('salesrule/rule');
$shoppingCartPriceRule
  ->setName("Discount for low HDI countries")
  ->setDescription("20% discount for low HDI countries")
  ->setIsActive(1)
  ->setWebsiteIds(array(1))
  ->setCustomerGroupIds(array(0, 1, 2, 3))
  ->setFromDate('')
  ->setToDate('')
  ->setSortOrder('')
  ->setSimpleAction('by_percent')
  ->setDiscountAmount(20)
  ->setSortOrder(5)
  ->setStopRulesProcessing(0);
$shoppingCartPriceRule->getConditions()->setAggregator('any');
foreach ($countries as $country) {
  $cond = Mage::getModel('salesrule/rule_condition_address')
    ->setType('salesrule/rule_condition_address')
    ->setAttribute('country_id')
    ->setOperator('==')
    ->setValue($country);
  $shoppingCartPriceRule->getConditions()->addCondition($cond);
}
$shoppingCartPriceRule->save();

$installer->endSetup();


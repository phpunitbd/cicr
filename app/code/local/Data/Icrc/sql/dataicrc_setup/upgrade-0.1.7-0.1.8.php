<?php

$installer = $this;
$installer->startSetup();

## Add South Sudan into countries
$installer->run("INSERT IGNORE INTO `directory_country` (`country_id`, `iso2_code`, `iso3_code`) VALUES ('SS', 'SS', 'SSD');");

## Set low-idh countries
$countries = array('AF','AO','AM','BD','BZ','BJ','BT','BO','BF','BI','KH','CM','CV','CF','TD','KM','CD','CG','CI','DJ','EG','SV','ER','ET','FJ','GM','GE','GH','GT','GN','GW','GY','HT','HN','IN','ID','IQ','KE','KI','KP','KG','LA','LS','LR','MG','MW','ML','MH','MR','FM','MD','MN','MA','MZ','MM','NP','NI','NE','NG','PK','PG','PY','PH','RW','WS','ST','SN','SL','SB','SO','SS','LK','SD','SZ','SY','TJ','TZ','TL','TG','TO','TM','TV','UG','UA','UZ','VU','VN','YE','ZM','ZW');
$shoppingCartPriceRule = Mage::getModel('salesrule/rule')->load("Discount for low HDI countries", 'name'); // todo: load

if ($shoppingCartPriceRule->getId()) {

  $shoppingCartPriceRule->getConditions()->setConditions(array()); // remove current conditions

  foreach ($countries as $country) {
    $cond = Mage::getModel('salesrule/rule_condition_address')
      ->setType('salesrule/rule_condition_address')
      ->setAttribute('country_id')
      ->setOperator('==')
      ->setValue($country);
    $shoppingCartPriceRule->getConditions()->addCondition($cond);
  }

  $shoppingCartPriceRule->save();

}

$installer->endSetup();


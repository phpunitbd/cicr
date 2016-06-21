<?php

$this->startSetup();

$attrs = array('lang', 'film_system', 'film_definition', 'film_format', 'storloc');
foreach ($attrs as $a) {
  $att = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $a);
  if (!$att->getId())
    continue;
  $f = $att->getBackendType();
  if ($f != 'varchar')
    continue;
  $id = $att->getId();
  $sql = "INSERT INTO catalog_product_entity_int(entity_type_id, attribute_id, store_id, entity_id, value)" .
    " SELECT entity_type_id, attribute_id, store_id, entity_id, value FROM catalog_product_entity_varchar" .
    " WHERE attribute_id = $id ON DUPLICATE KEY UPDATE value = VALUES(value)" ;
  $this->run($sql);
  $att->setBackendType('int')->save();
}

$this->endSetup();


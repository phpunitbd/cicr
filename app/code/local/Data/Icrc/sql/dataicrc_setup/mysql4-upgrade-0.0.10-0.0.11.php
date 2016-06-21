<?php

$this->startSetup();

$this->run('insert into catalog_category_entity_varchar(entity_type_id,attribute_id,store_id,entity_id,value) select entity_type_id,attribute_id,0,entity_id,value from catalog_category_entity_varchar where attribute_id = 133 and store_id = 1');

$this->endSetup();


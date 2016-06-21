<?php

$installer = $this;
$installer->startSetup();

$installer->run("INSERT IGNORE INTO `directory_country` (`country_id`, `iso2_code`, `iso3_code`) VALUES ('XZ', 'XZ', 'UNK');");

$installer->endSetup();


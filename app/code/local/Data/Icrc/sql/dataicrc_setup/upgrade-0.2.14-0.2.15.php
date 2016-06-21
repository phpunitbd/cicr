<?php

$this->startSetup();

$helper = Mage::helper('data_icrc/update');
list($en, $fr, $int) = $helper->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('system/cron/schedule_generate_every', '15', 'default', 0);
$conf->saveConfig('system/cron/schedule_ahead_for', '600', 'default', 0);
$conf->saveConfig('system/cron/schedule_lifetime', '30', 'default', 0);
$conf->saveConfig('system/cron/history_cleanup_every', '600', 'default', 0);
$conf->saveConfig('system/cron/history_success_lifetime', '2880', 'default', 0); # 2 days
$conf->saveConfig('system/cron/history_failure_lifetime', '10080', 'default', 0); # 7 days

$this->endSetup();


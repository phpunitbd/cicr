<?php

$installer = $this;
$installer->startSetup();

$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();
$en = Mage::getModel('core/store')->load('default', 'code')->getId();
//$int = Mage::getModel('core/store')->load('internal', 'code')->getId();

$frs = Mage::getModel('sitemap/sitemap');
$frs->setStoreId($fr)->setSitemapFilename('sitemap.xml')->setSitemapPath('/sitemap/fr/')->save();

$ens = Mage::getModel('sitemap/sitemap');
$ens->setStoreId($en)->setSitemapFilename('sitemap.xml')->setSitemapPath('/sitemap/en/')->save();

//$ints = Mage::getModel('sitemap/sitemap');
//$ints->setStoreId($int)->setSitemapFilename('sitemap.xml')->setSitemapPath('/sitemap/int/')->save();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('sitemap/generate/enabled', '1', 'default', 0);
$conf->saveConfig('sitemap/generate/error_email_identity', 'general', 'default', 0);
$conf->saveConfig('sitemap/generate/error_email_template', 'sitemap_generate_error_email_template', 'default', 0);
$conf->saveConfig('sitemap/generate/frequency', 'D', 'default', 0);
$conf->saveConfig('sitemap/generate/time', '00,00,00', 'default', 0);
$conf->saveConfig('sitemap/page/changefreq', 'daily', 'default', 0);
$conf->saveConfig('sitemap/page/priority', '0.25', 'default', 0);
$conf->saveConfig('sitemap/product/priority', '1', 'default', 0);
$conf->saveConfig('sitemap/product/changefreq', 'daily', 'default', 0);
$conf->saveConfig('sitemap/category/changefreq', 'daily', 'default', 0);
$conf->saveConfig('sitemap/category/priority', '0.5', 'default', 0);

$installer->endSetup();


<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$helper = Mage::helper('data_icrc/update');
list($en, $fr, $int) = $helper->getStoreIds();

$publication_set = $helper->getAttributeSetId('publications');
$ebook_set = $helper->getAttributeSetId('ebook');
$film_set = $helper->getAttributeSetId('films');
$gift_set = $helper->getAttributeSetId('gift');
$ex_set = $helper->getAttributeSetId('exhibition');

$orphan = array('Orphan' => array('orphan', 'boolean', 'int', true, 'add' => array('used_in_product_listing')));
$helper->recordAttr($setup, $orphan, $publication_set);
$orphan['Orphan'][1] = null;
$helper->recordAttr($setup, $orphan, $ebook_set);
$helper->recordAttr($setup, $orphan, $film_set);
$helper->recordAttr($setup, $orphan, $gift_set);
$helper->recordAttr($setup, $orphan, $ex_set);

$setup->endSetup();


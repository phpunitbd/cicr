<?php

$installer = $this;
$installer->startSetup();

$resources = array(
'admin/system',
'admin/system/config',
'admin/system/config/general',
'admin/system/config/trans_email',
'admin/system/config/catalog',
'admin/system/config/sales_email',
'admin/system/currency',
'admin/system/currency/rates',
'admin/cms',
'admin/cms/block',
'admin/cms/page',
'admin/cms/page/save',
'admin/customer',
'admin/customer/manage',
'admin/customer/online',
'admin/catalog',
'admin/catalog/products',
'admin/catalog/categories',
'admin/promo',
'admin/promo/catalog',
'admin/promo/quote',
'admin/sales',
'admin/sales/order',
'admin/sales/order/actions',
'admin/sales/order/actions/create',
'admin/sales/order/actions/view',
'admin/sales/order/actions/email',
'admin/sales/order/actions/reorder',
'admin/sales/order/actions/edit',
'admin/sales/order/actions/cancel',
'admin/sales/order/actions/review_payment',
'admin/sales/order/actions/capture',
'admin/sales/order/actions/invoice',
'admin/sales/order/actions/creditmemo',
'admin/sales/order/actions/hold',
'admin/sales/order/actions/unhold',
'admin/sales/order/actions/ship',
'admin/sales/order/actions/comment',
'admin/sales/order/actions/emails',
'admin/sales/invoice',
'admin/sales/shipment',
'admin/sales/creditmemo',
'admin/data_icrc',
'admin/data_icrc/datastudio'
);

Mage::helper('data_icrc/admin')->changeICRCAdminRole($resources);

$installer->endSetup();


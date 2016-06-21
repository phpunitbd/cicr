<?php

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE  `icrc_payment_pending` (
`authorize_id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`order_id` int(10) unsigned NOT NULL ,
`email` VARCHAR( 255 ) NOT NULL ,
`token` VARCHAR( 50 ) NOT NULL ,
FOREIGN KEY (`order_id`) REFERENCES `sales_flat_order` (`entity_id`)
) ENGINE = INNODB COMMENT =  'ICRC payment autorization pending';

INSERT INTO `order_attribute` 
(`order_attribute_id`, `title`, `content`, `status`, `created_time`, `update_time`) VALUES 
(NULL, 'Unit or Delegation (shipping)', '', '1', NOW(), NOW()), 
(NULL, 'Comment (shipping)', '', '0', NOW(), NOW()), 
(NULL, 'Validation Email', '', '1', NOW(), NOW());
");

$tmpl = Mage::getModel('core/email_template');
$tmpl->setTemplateCode('order_validation')
     ->setTemplateText("<!--@styles
body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }
@-->

<body style=\"background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;\">
<div style=\"background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:5px 10px;\">
<h1>The following order must be validated.</h1>

<h2>Order by {{htmlescape var=\$order.getCustomerName()}}</h2>

<h2>Shipping details</h2>
<ul>
<li>Unit or delegation: {{var ship_unit}}</li>
{{depend ship_com}}
<li>Comment: {{var ship_com}}</li>
{{/depend}}
</ul>

<h2>Payment details</h2>
<ul>
<li>Unit or delegation: {{var bill_unit}}</li>
<li>Cost Center: {{var bill_cc}}</li>
<li>Objective Code: {{var bill_oc}}</li>
{{depend bill_com}}
<li>Comment: {{var bill_com}}</li>
{{/depend}}
</ul>

{{layout handle=\"sales_email_order_items\" order=\$order}}

<p>Click on the following link to validate the order: <a href=\"{{var validurl}}\">VALIDATE</a></p>

<p>Click on the following link to cancel the order: <a href=\"{{var refuseurl}}\">CANCEL</a></p>
</div>
</body>")
     ->setTemplateType(0)
     ->setTemplateSubject('Order Validation')
     ->save();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('shipping/option/checkout_multiple', '0', 'default', 0);
$conf->saveConfig('general/country/default', 'CH', 'default', 0);
$conf->saveConfig('general/region/state_required', 'CA,US', 'default', 0);
$conf->saveConfig('general/region/display_all', '0', 'default', 0);


$installer->endSetup();


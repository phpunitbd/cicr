<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Test extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
	$orders = Mage::getModel('sales/order')
		->getCollection()
		->addAttributeToFilter('increment_id', 100002034);
	foreach ($orders as $order) {
		//var_dump($order);
		$gt = $order->getGrandTotal();
		var_dump($gt);
		$res = intval(Mage::helper('saferpay')->round($gt, 2) * 100);
		var_dump($res);
		$r2 = Mage::helper('saferpay')->round($gt, 2);
		$r3 = $r2 * 100;
		$r4 = intval($r3);
		$r5 = round($r3,0);
		$r6 = intval(($r2 + 0.1) * 100);
		var_dump(array($r2,$r3,(string)$r3,intval((string)$r3),
			$r4,intval($r5),$r6));
	}

	
    }

}

$shell = new Mage_Shell_Test();
$shell->run();

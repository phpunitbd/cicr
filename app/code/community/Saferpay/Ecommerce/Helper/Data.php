<?php
/**
 * Saferpay Ecommerce Magento Payment Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Saferpay Business to
 * newer versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright Copyright (c) 2011 Openstream Internet Solutions (http://www.openstream.ch)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Saferpay_Ecommerce_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getSetting($key)
	{
		return Mage::getStoreConfig('saferpay/settings/' . $key);
	}
	
	/**
	 * Unified round() implementation for the Saferpay extension
	 *
	 * @param mixed $value String, Integer or Float
	 * @return float
	 */
	public function round($value)
	{
		return Zend_Locale_Math::round($value, 2);
	}
	
	public function process_url($url, $params = array()){
		foreach ($params as $k => $v){
			$url .= strpos($url, '?') === false ? '?' : '&';
			$url .= sprintf("%s=%s", $k, urlencode($v));
		}
	
		$adapter = Mage::getStoreConfig('saferpay/settings/http_client_adapter');
		if ('stream_wrapper' !== $adapter && class_exists($adapter, true) && ($adapter = new $adapter) && $adapter instanceof Zend_Http_Client_Adapter_Interface){
			$client = new Zend_Http_Client($url, array('adapter' => $adapter));
			$contents = $client->request()->getBody();
		}else{
			$contents = file_get_contents($url);
		}
		return trim($contents);
	}
	
	public function _parseResponseXml($xml)
	{
		if($xml = simplexml_load_string($xml)){
			$data = (array) $xml->attributes();
			return $data['@attributes'];
		}else{
			return 0;
		}
	}
	
	/**
	 * Seperate the result status and the xml in the response
	 *
	 * @param string $response
	 * @return array
	 */
	public function _splitResponseData($response)
	{
		if (($pos = strpos($response, ':')) === false){
			$status = $response;
			$ret = array();
		}else{
			$status = substr($response, 0, strpos($response, ':'));
			$params = substr($response, strpos($response, ':')+1);
			if(preg_match('/&/', $params)){
			 $params = explode('&', $params);
			 $ret = array();
			 foreach($params as $param){
			  list($key, $val) = explode('=', $param);
			  if($key && $val){
			   $ret[$key] = $val;
			  }
			 }
			}else{
			 $ret = $params;
			}
		}
		return array($status, $ret);
	}	
}

<?php

class Data_Icrc_Model_Sales_Order_Api extends Mage_Sales_Model_Order_Api
{
  protected function _getOrderAttributes(&$order)
  {
    if ($order AND $order['quote_id']) {
      $attribultes = array();
      foreach (Mage::helper('data_icrc/attributes')->getOrderAttributes($order['quote_id'], true) as $attr) {
        $a = new stdClass();
        $a->key = $attr->getTitle();
        $a->value = $attr->getValue();
        $attributes[] = $a;
      }
      if (count($attributes) > 0) $order['additional_attributes'] = $attributes;
    }
    return $order;
  }

  public function items($filters = null)
  {
    $orders = parent::items($filters);

    foreach ($orders as &$o) {
      $this->_getOrderAttributes($o);
    }

    return $orders;
  }

  public function info($orderIncrementId)
  {
    $result = parent::info($orderIncrementId);
    return $this->_getOrderAttributes($result);
  }
}


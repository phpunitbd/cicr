<?php

class Data_Icrc_Helper_Attributes extends Mage_Core_Helper_Abstract
{
  public function getCustomerTypeId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::CUSTOMER_TYPE]; }
  public function getUnitId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::BILLING_UNIT]; }
  public function getCCId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::BILLING_COST_CENTER]; }
  public function getOCId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::BILLING_OBJECTIVE_CODE]; }
  public function getComId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::BILLING_COMMENT]; }
  public function getShippingUnitId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::SHIPPING_UNIT]; }
  public function getShippingComId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::SHIPPING_COMMENT]; }
  public function getShippingTypeId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::SHIPPING_TYPE]; }
  public function getValidationemailId() { if ($this->_attrIds === null) $this->_loadAttrIds(); return $this->_attrIds[self::VALIDATION_EMAIL]; }

  const CUSTOMER_TYPE = 'Customer Type';
  const VALIDATION_EMAIL = 'Validation Email';
  const BILLING_UNIT = 'Unit or Delegation';
  const BILLING_COST_CENTER = 'Cost Center';
  const BILLING_OBJECTIVE_CODE = 'Objective Code';
  const BILLING_COMMENT = 'Comment';
  const SHIPPING_UNIT = 'Unit or Delegation (shipping)';
  const SHIPPING_TYPE = 'Destination Type';
  const SHIPPING_COMMENT = 'Comment (shipping)';
  protected $_attrIds = null;

  protected function _loadAttrIds() {
    $this->_attrIds = array();
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $result = $read->query('select order_attribute_id, title from order_attribute');
    while ($row = $result->fetch()) {
      $this->_attrIds[$row['title']] = $row['order_attribute_id'];
    }
  }

  public function getCustomerTypeSelectOptions() {
    $return = array();
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $sql = "select content from order_attribute where order_attribute_id = ?";
    $result = $read->query($sql, array($this->getCustomerTypeId()));
    if ($row = $result->fetch()) {
      foreach (explode(',', $row['content']) as $val) {
        $return[] = array('value' => $val, 'label' => $this->getLabel($val));
      }
    }
    return $return;
  }

  public function getCurrentCustomerTypeValue($quote) {
    if ($quote == null)
      return null;
    if (is_object($quote)) {
      if ($quote->getId() <= 0)
        return null;
      else
        $quoteId = $quote->getId();
    }
    elseif (is_numeric($quote))
      $quoteId = (int)$quote;
    if ($quoteId < 1)
      return null;
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $sql = "select `value` from `order_attribute_value` where `order_attribute_id` = ? and `quote_id` = ? ";
    $values = array($this->getCustomerTypeId(), $quoteId);
    $user = Mage::getSingleton('customer/session');
    if ($user && $user->isLoggedIn()) {
      $values[] = $this->getCustomerTypeId();
      $values[] = $user->getId();
      $sql .= "union all select `value` from `order_attribute_defaults` where `order_attribute_id` = ? and `user_id` = ?";
    }
    $result = $read->query($sql, $values);
    if ($row = $result->fetch()) return $row['value'];
    else return null;
  }

  public function setCustomerTypeValue($quote, $value) {
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "insert into `order_attribute_value` (`order_attribute_id`, `quote_id`, `value`) ".
           "values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    $params = array($this->getCustomerTypeId(), $quote->getId(), $value);
    $write->query($sql, $params);
    Data_Icrc_Helper_Debug::msgdump($sql, $params);
    if (array_key_exists($quote->getId(), $this->quote_attr_cache))
      unset($this->quote_attr_cache[$quote->getId()]);
  }

  public function setDefaultCustomerTypeValue($user, $value) {
    if (!$user) $id = Mage::getSingleton('customer/session')->getId();
    elseif (is_object($user)) $id = $user->getId();
    else $id = $user;
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "insert into `order_attribute_defaults` (`order_attribute_id`, `user_id`, `value`) ".
           "values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    $write->query($sql, array($this->getCustomerTypeId(), $id, $value));
  }

  public function getDefaultCustomerTypeValue($user = null) {
    if (!$user) $id = Mage::getSingleton('customer/session')->getId();
    elseif (is_object($user)) $id = $user->getId();
    else $id = $user;
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $sql = "select `value` from `order_attribute_defaults` where `order_attribute_id` = ? and `user_id` = ?";
    $result = $read->query($sql, array($this->getCustomerTypeId(), $id));
    if ($row = $result->fetch()) return $row['value'];
    else return null;
  }

  /** @deprecated */
  public function getOrderAtrributes($order) {
    return $this->getOrderAttributes($order);
  }

  private $quote_attr_cache = array();
  public function getOrderAttributes($order, $technical = false) {
    if (is_object($order)) $quote = $order->getQuoteId();
    else $quote = $order;
    $ret = array();
    if (!$quote) return $ret;
    if (array_key_exists($quote, $this->quote_attr_cache))
      return $this->quote_attr_cache[$quote];
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $sql = "select a.title, v.value from order_attribute a, order_attribute_value v where a.order_attribute_id = v.order_attribute_id and quote_id = ?";
    $result = $read->query($sql, array($quote));
    while ($row = $result->fetch()) {
      $att = new Varien_Object();
      $att->setTitle($row['title']);
      if (!$technical) $att->setValue($this->getLabel($row['value']));
      else $att->setValue($row['value']);
      $ret[] = $att;
    }
    $this->quote_attr_cache[$quote] = $ret;
    return $ret;
  }

  public function getOrderAttributeValue($title, $order, $technical = false) {
    $attrs = $this->getOrderAttributes($order, $technical);
    foreach ($attrs as $attr) {
      if ($attr->getTitle() == $title)
        return $attr->getValue();
    }
    return null;
  }

  private $_labels = array();

  public function getLabel($code) {
    $s = Mage::app()->getStore();
    $store = $s->getId();
    if ($s->getCode() == 'internal') $store = 0;
    if (array_key_exists($code, $this->_labels)) {
      if (array_key_exists($store, $this->_labels[$code])) {
        return $this->_labels[$code][$store];
      }
    }
    else
      $this->_labels[$code] = array();
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $sql = "select label from order_attribute_label where code = ? and store_view = ?";
    $result = $read->query($sql, array($code, $store));
    if ($row = $result->fetch())
      $this->_labels[$code][$store] = $row['label'];
    else
      $this->_labels[$code][$store] = $code;
    return $this->_labels[$code][$store];
  }

  public function loadAdminLabels() {
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $sql = "select code, label from order_attribute_label where store_view = 0";
    $result = $read->query($sql);
    while ($row = $result->fetch())
      $this->_labels[$row['code']][0] = $row['label'];
  }

  public function setIcrcPaymentInfo($quote, $data) {
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "insert into `order_attribute_value` (`order_attribute_id`, `quote_id`, `value`) ".
           "values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    if (!array_key_exists('icrc_unit', $data))
      throw new Mage_Core_Exception('Cannot find billing Delegation or Unit');
    if (!array_key_exists('icrc_cost_center', $data))
      throw new Mage_Core_Exception('Cannot find Cost Center');
    if (!array_key_exists('icrc_objective_code', $data))
      throw new Mage_Core_Exception('Cannot find Objective Code');
    $write->query($sql, array($this->getUnitId(), $quote->getId(), $data['icrc_unit']));
    $write->query($sql, array($this->getCCId(), $quote->getId(), $data['icrc_cost_center']));
    $write->query($sql, array($this->getOCId(), $quote->getId(), $data['icrc_objective_code']));
    if (array_key_exists('icrc_com', $data))
      $write->query($sql, array($this->getComId(), $quote->getId(), $data['icrc_com']));
    if (array_key_exists($quote->getId(), $this->quote_attr_cache))
      unset($this->quote_attr_cache[$quote->getId()]);
    
    /* Save in address book */
    if (array_key_exists('save_in_address_book', $data)) {
      $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
      $info = Mage::getModel('data_icrc/customer_billing_info');
      if (array_key_exists('id', $data) && $data['id'] != 'new')
        $id = $data['id'];
      else
        $id = 0;
      if ($id) {
        $info->load($id);
        if (!$info->getId()) {
          throw new Mage_Core_Exception('Incorrect Id');
        }
        if ($info->getCustomerId() != $cid) {
          throw new Mage_Core_Exception('Cannot edit other customer info');
        }
      } else
        $info->setCustomerId($cid);
      $info->setUnit($data['icrc_unit'])->
             setObjectiveCode($data['icrc_objective_code'])->
             setCostCenter($data['icrc_cost_center'])->
             save();
    }
  }

  public function setIcrcPaymentInfoAdmin($quote, $data) {
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "insert into `order_attribute_value` (`order_attribute_id`, `quote_id`, `value`, `created_time`, `update_time`) ".
           "values (?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `update_time` = NOW()";
    if (array_key_exists('icrc_unit', $data)) {
      $write->query($sql, array($this->getUnitId(), $quote->getId(), $data['icrc_unit']));
    }
    if (array_key_exists('icrc_cost_center', $data)) {
      $write->query($sql, array($this->getCCId(), $quote->getId(), $data['icrc_cost_center']));
    }
    if (array_key_exists('icrc_objective_code', $data)) {
      $write->query($sql, array($this->getOCId(), $quote->getId(), $data['icrc_objective_code']));
    }
    if (array_key_exists('icrc_com', $data)) {
      $write->query($sql, array($this->getComId(), $quote->getId(), $data['icrc_com']));
    }
    if (array_key_exists($quote->getId(), $this->quote_attr_cache))
      unset($this->quote_attr_cache[$quote->getId()]);
    return $this;
  }

  public function setIcrcShippingInfo($quote, $data) {
    Data_Icrc_Helper_debug::msgdump('setIcrcShippingInfo', $data);
    Data_Icrc_Helper_debug::dump(array_key_exists('icrc_type', $data));
    Data_Icrc_Helper_debug::dump(empty($data['icrc_type']));
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "insert into `order_attribute_value` (`order_attribute_id`, `quote_id`, `value`) ".
           "values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    if (!array_key_exists('icrc_unit', $data) || empty($data['icrc_unit']))
      throw new Mage_Core_Exception('Cannot find shipping Delegation or Unit');
    $write->query($sql, array($this->getShippingUnitId(), $quote->getId(), $data['icrc_unit']));
    if (array_key_exists('icrc_com', $data) && !empty($data['icrc_com']))
      $write->query($sql, array($this->getShippingComId(), $quote->getId(), $data['icrc_com']));
    if (array_key_exists('icrc_type', $data) && !empty($data['icrc_type'])) {
      Data_Icrc_Helper_debug::dump($this->_attrIds);
      Data_Icrc_Helper_debug::msg('foo');
      Data_Icrc_Helper_debug::msg($this->getShippingTypeId());
      Data_Icrc_Helper_debug::msg('foo');
      Data_Icrc_Helper_debug::msg($quote->getId());
      Data_Icrc_Helper_debug::msg('foo');
      Data_Icrc_Helper_debug::msgdump($sql, array($this->getShippingTypeId(), $quote->getId(), $data['icrc_type']));
      $write->query($sql, array($this->getShippingTypeId(), $quote->getId(), $data['icrc_type']));
      Data_Icrc_Helper_debug::msg('bar');
    }
    if (array_key_exists($quote->getId(), $this->quote_attr_cache))
      unset($this->quote_attr_cache[$quote->getId()]);
    return $this;
  }

  public function setIcrcShippingInfoAdmin($quote, $data) {
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "insert into `order_attribute_value` (`order_attribute_id`, `quote_id`, `value`, `created_time`, `update_time`) ".
           "values (?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `update_time` = NOW()";
    if (array_key_exists('icrc_unit', $data)) {
      $write->query($sql, array($this->getShippingUnitId(), $quote->getId(), $data['icrc_unit']));
    }
    if (array_key_exists('icrc_com', $data)) {
      $write->query($sql, array($this->getShippingComId(), $quote->getId(), $data['icrc_com']));
    }
    if (array_key_exists('icrc_type', $data) && !empty($data['icrc_type'])) {
      $write->query($sql, array($this->getShippingTypeId(), $quote->getId(), $data['icrc_type']));
    }
    if (array_key_exists($quote->getId(), $this->quote_attr_cache))
      unset($this->quote_attr_cache[$quote->getId()]);
    return $this;
  }

  public function setIcrcValidationInfo($quote, $data) {
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $sql = "insert into `order_attribute_value` (`order_attribute_id`, `quote_id`, `value`) ".
           "values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    if (!array_key_exists('icrc_email', $data))
      throw new Mage_Core_Exception('Cannot get validation e-mail');
    if (!preg_match('/@icrc.org$/i', $data['icrc_email'])) {
      $invalid = true;
      if (Mage::getStoreConfig('icrc/janus/enable') == 0) // If Janus is disabled (=dev), allow data.fr
        if (preg_match('/@data.fr$/i', $data['icrc_email']))
          $invalid = false;
      if ($invalid)
        throw new Mage_Core_Exception('Invalid validation e-mail');
    }
    $write->query($sql, array($this->getValidationemailId(), $quote->getId(), $data['icrc_email']));
    if (array_key_exists($quote->getId(), $this->quote_attr_cache))
      unset($this->quote_attr_cache[$quote->getId()]);
    return $this;
  }
}

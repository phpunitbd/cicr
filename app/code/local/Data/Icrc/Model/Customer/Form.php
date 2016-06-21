<?php

class Data_Icrc_Model_Customer_Form extends Mage_Customer_Model_Form
{
  public function extractData(Zend_Controller_Request_Http $request, $scope = null, $scopeOnly = true) {
    $data = parent::extractData($request, $scope, $scopeOnly);
    if (Mage::helper('data_icrc/internal')->isInternal() || $this->isAdminAndInternalCustomer()) {
      if ($scope != null) {
        if (strpos($scope, '/') !== false) {
          $params = $request->getParams();
          $parts = explode('/', $scope);
          foreach ($parts as $part) {
            if (isset($params[$part])) {
              $params = $params[$part];
            } else {
              $params = array();
            }
          }
           if (array_key_exists('icrc_type', $params)) {
            $data['icrc_type'] = $params['icrc_type'];
            $data['icrc_unit'] = $params['icrc_unit'];
          }
        }
      } else {
        $data['icrc_type'] = $request->getPost('icrc_type');
        $data['icrc_unit'] = $request->getPost('icrc_unit');
        if (empty($data['icrc_type'])) {
          $params = $request->getParams();
          if (array_key_exists('icrc_type', $params)) {
              $data['icrc_type'] = $params['icrc_type'];
              $data['icrc_unit'] = $params['icrc_unit'];
            }
        }
      }
      if (array_key_exists('icrc_type', $data) && $data['icrc_type'] == 'unit') {
        $add_data = Mage::helper('data_icrc/internal')->getUnitAdressData($data['icrc_unit']);
        foreach ($add_data as $key => $value) {
          if (!array_key_exists($key, $data) || $data[$key] == '' || $data[$key] == null)
            $data[$key] = $value;
          if (is_array($data[$key]) && is_array($value))
            foreach ($value as $subkey => $subvalue)
              if (!array_key_exists($subkey, $data[$key]) || $data[$key][$subkey] == '' || $data[$key][$subkey] == null)
                $data[$key][$subkey] = $subvalue;
        }
      }
    }
    return $data;
  }
  
  public function compactData(array $data) {
    parent::compactData($data);
    foreach (array('icrc_type', 'icrc_unit') as $attr) {
      if (array_key_exists($attr, $data))
        $this->getEntity()->setData($attr, $data[$attr]);
    }
    return $this;
  }
  
  protected function isAdminAndInternalCustomer() {
    if (Mage::app()->getStore()->getId() != 0)
      return false;
    $customer = Mage::registry('current_customer');
    if ($customer && $customer->getWebsiteId() == Mage::helper('data_icrc/internal')->getInternalWebsiteId())
      return true;
    return false;
  }
}

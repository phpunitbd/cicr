<?php

class ds_soap_param {
  var $name;
  var $value;
  function ds_soap_param($n, $v) {
    $this->name = $n;
    $this->value = $v;
  }
}

class Data_Icrc_Helper_Datastudio extends Mage_Core_Helper_Abstract
{
  const ENDPOINT = 'http://localhost:8118';

  private $_id = null;
  private $_client = null;

  function run($project, array $args, $login='Oper', $passwd='oper') {
    if (!$this->_id)
      $this->login($login, $passwd);
    $params = array('hashcode' => $this->_id->hashcode, 'name' => $project);
    $ret = $this->_getClient()->getProjectLabeledInfo($params);
    if (!property_exists($ret, 'params'))
      $ret->params = array();
    foreach ($args as $name => $value) {
      if (is_array($ret->params)) {
        foreach ($ret->params as &$pp) {
          if ($pp->name->code == $name) {
            $pp->value = $value;
            break;
          }
        }
      } else {
        if ($ret->params->name->code == $name)
          $ret->params->value = $value;
      }
    }
    if (!is_array($ret->params))
      $ret->params = array($ret->params);
    $p = array('hashcode' => $this->_id->hashcode, 'name' => $project, 'params' => $ret->params, 'synchrone' => true);
    $ret = $this->_getClient()->runProjectLabeled($p);
    $info = array('hashcode' => $this->_id->hashcode, 'execId' => $ret->execId);
    $return = $this->_getClient()->getExecStatus(array_merge($info, array('fromLine' => 0)));
    $val = 'return-value';
    $returnvalue = $return->$val;
    if ($returnvalue < 0) {
      Mage::getSingleton('adminhtml/session')->setLastTrace($return->messages);
      Mage::register('trace_saved', true);
      Mage::throwException($this->__("Project failed with error %d", $returnvalue));
    }
    $data = $this->_getClient()->getExecReturnData($info);
    return $data;
  }

  function login($login='Oper', $passwd='oper') {
    if ($this->_id) return;
    $this->_id = $this->_getClient()->login(array('username' => $login, 'password' => $passwd));
    //Mage::getSingleton('adminhtml/session')->addNotice(Zend_Debug::dump($ret, null, false));
    //$this->_id = $ret->hashcode;
  }

  function listProjects() {
    if (!$this->_id)
      $this->login();
    $ret = $this->_getClient()->getProjectLabeledList($this->_id);
    //Mage::getSingleton('adminhtml/session')->addNotice(Zend_Debug::dump($ret, null, false));
    //return array();
    return $ret->projects;
  }

  function getProjectParameters($project) {
    if (!$this->_id)
      $this->login();
    $params = array('hashcode' => $this->_id->hashcode, 'name' => $project);
    $ret = $this->_getClient()->getProjectLabeledInfo($params);
    if (empty($ret->params)) $ret->params = array();
    if (is_object($ret->params)) $ret->params = array($ret->params);
    return $ret;
  }

  function getParamValues($param) {
    if (!$this->_id)
      $this->login();
    $params = array('hashcode' => $this->_id->hashcode, 'parameter' => $param);
    $ret = $this->_getClient()->getParamValues($params);
    if (empty($ret->mStrings)) $ret->mStrings = array();
    if (is_object($ret->mStrings)) $ret->mStrings = array($ret->mStrings);
    return $ret->mStrings;
  }

  private function _getClient() {
    if (!$this->_client) {
      ini_set("default_socket_timeout", 1800); // Sets soap timeout to 30 min
      $file = dirname(__FILE__) . DS . 'Datastudio.wsdl';
      if (!file_exists($file))
        foreach (array('local', 'community', 'core') as $part) {
          $file = Mage::getBaseDir('code') . DS . $part . DS . 'Data' . DS . 'Icrc' . DS . 'Helper' . DS . 'Datastudio.wsdl';
          if (file_exists($file)) break;
        }
      if (!file_exists($file))
        Mage::throwException($this->__('Cannot find WSDL file'));

      $endpoint = Mage::getStoreConfig('icrc/datastudio/endpoint');
      if (empty($endpoint))
        $endpoint = self::ENDPOINT;

      Data_Icrc_Helper_Debug::msg("endpoint: ${endpoint}");

      $this->_client = new SoapClient($file, array(
          'location' => $endpoint,
          'connection_timeout' => 10
        ));
    }
    return $this->_client;
  }
  
  public function getLogSince($since = 'DATE_SUB(NOW(), INTERVAL 2 HOUR)') {
    if (is_numeric($since))
      $since = "DATE_SUB(NOW(), INTERVAL $since HOUR)";
    $collection = Mage::getModel('data_icrc/datastudio_log')->getCollection();
    $collection->getSelect()->where('`date` > ' . $since);
    $events = array();
    foreach ($collection as $event)
      $events[] = $event;
    return $events;
  }
}


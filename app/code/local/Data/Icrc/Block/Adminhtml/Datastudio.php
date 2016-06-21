<?php

class Data_Icrc_Block_Adminhtml_Datastudio extends Mage_Adminhtml_Block_Template
{
  public function __construct()
  {
    parent::__construct();
    $this->setTemplate('datastudio.phtml');
  }

  protected function getProjects()
  {
    try {
      return Mage::helper('data_icrc/datastudio')->listProjects();
    } catch (Exception $e) {
      Mage::logException($e);
      Mage::getSingleton('adminhtml/session')->addError(Zend_Debug::dump($e, null, false));
      return array();
    }
  }
}


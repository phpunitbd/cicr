<?php

class Data_Icrc_Block_Adminhtml_Config extends Mage_Adminhtml_Block_System_Config_Form
{
  /** This function is intended to check scope visibility, but we'll detour it to check ACLs */
  protected function _canShowField($field)
  {
    if ($field->acl) {
      $session = Mage::getSingleton('admin/session');
      try {
      $resourceId = $session->getData('acl')->get($field->acl)->getResourceId();
      if (!$session->isAllowed($resourceId))
        return false;
      } catch (Zend_Acl_Exception $ex) {
        error_log($ex->getMessage());
        return false;
      }
    }
    return parent::_canShowField($field);
  }
}

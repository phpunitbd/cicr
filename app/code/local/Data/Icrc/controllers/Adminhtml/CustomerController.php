<?php

require_once 'Mage/Adminhtml/controllers/CustomerController.php';
class Data_Icrc_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
  public function newBillingInfoAction() {
    if (!$this->_validateFormKey()) {
      return $this->_redirect('*/*/');
    }
    if ($this->getRequest()->isPost()) {
      $cid = $this->getRequest()->getParam('customer_id');
      $info = Mage::getModel('data_icrc/customer_billing_info');
      $id = $this->getRequest()->getParam('bid');
      if ($id) {
        $info->load($id);
        if (!$info->getId()) {
          $this->_getSession()->addError('Incorrect Id');
          return $this->_redirectError(Mage::helper("adminhtml")->getUrl('*/*/edit', array('_secure' => true, 'id' => $cid)));
        }
        if ($info->getCustomerId() != $cid) {
          $this->_getSession()->addError('Cannot edit other customer info');
          return $this->_redirectError(Mage::helper("adminhtml")->getUrl('*/*/edit', array('_secure' => true, 'id' => $cid)));
        }
      } else
        $info->setCustomerId($cid);
      $errors = array();
      $unit = $this->getRequest()->getParam('unit');
      if (!$unit) $errors[] = 'Unit or delegation field must be set';
      $cc = $this->getRequest()->getParam('cost_center');
      if (!$cc) $errors[] = 'Cost center field must be set';
      $oc = $this->getRequest()->getParam('objective_code');
      if (!$oc) $errors[] = 'Objective code field must be set';
      if (count($errors)) {
        foreach ($errors as $errorMessage) {
          $this->_getSession()->addError($errorMessage);
          return $this->_redirectError(Mage::helper("adminhtml")->getUrl('*/*/edit', array('_secure' => true, 'id' => $cid)));
        }
      }
      $info->setUnit($unit)->setObjectiveCode($oc)->setCostCenter($cc)->save();
      $this->_getSession()->addSuccess($this->__('The billing informations has been saved.'));
      $this->_redirectSuccess(Mage::helper("adminhtml")->getUrl('*/*/edit', array('_secure' => true, 'id' => $cid)));
    }
  }
    
  public function deleteBillingInfoAction()
  {
    $cid = $this->getRequest()->getParam('customer_id');
    $iid = $this->getRequest()->getParam('info_id');
    $info = Mage::getModel('data_icrc/customer_billing_info')->load($iid);
    if (!$info->getId()) {
      $this->_getSession()->addError('Incorrect Id');
      return $this->_redirectError(Mage::helper("adminhtml")->getUrl('*/*/edit', array('_secure' => true, 'id' => $cid)));
    }
    if ($info->getCustomerId() != $cid) {
      $this->_getSession()->addError('Cannot remove other customer info');
      return $this->_redirectError(Mage::helper("adminhtml")->getUrl('*/*/edit', array('_secure' => true, 'id' => $cid)));
    }
    $info->delete();
    $this->_getSession()->addSuccess('Billing information removed');
    $this->_redirectSuccess(Mage::helper("adminhtml")->getUrl('*/*/edit', array('_secure' => true, 'id' => $cid)));
  }
}

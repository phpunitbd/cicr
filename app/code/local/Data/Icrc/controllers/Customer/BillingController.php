<?php

class Data_Icrc_Customer_BillingController extends Mage_Core_Controller_Front_Action
{
  public function preDispatch()
  {
    parent::preDispatch();
    if (!Mage::getSingleton('customer/session')->authenticate($this)) {
      $this->setFlag('', 'no-dispatch', true);
    }
    if (!Mage::helper('data_icrc/internal')->isInternal()) {
      $this->setFlag('', 'no-dispatch', true);
    }
  }
    
  public function indexAction()
  {
    $this->_layout();
    $this->renderLayout();
  }
    
  public function newAction()
  {
    $this->_layout();
    $this->renderLayout();
  }
    
  public function deleteAction()
  {
    $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
    $iid = $this->getRequest()->getParam('id');
    $info = Mage::getModel('data_icrc/customer_billing_info')->load($iid);
    if (!$info->getId()) {
      $this->_getSession()->addError('Incorrect Id');
      return $this->_redirectError(Mage::getUrl('*/*/'));
    }
    if ($info->getCustomerId() != $cid) {
      $this->_getSession()->addError('Cannot remove other customer info');
      return $this->_redirectError(Mage::getUrl('*/*/'));
    }
    $info->delete();
    $this->_getSession()->addSuccess('Billing information removed');
    return $this->_redirect('*/*/');
  }
  
  public function formPostAction()
  {
    if (!$this->_validateFormKey()) {
      return $this->_redirect('*/*/');
    }
    if ($this->getRequest()->isPost()) {
      $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
      $info = Mage::getModel('data_icrc/customer_billing_info');
      $id = $this->getRequest()->getParam('id');
      if ($id) {
        $info->load($id);
        if (!$info->getId()) {
          $this->_getSession()->addError('Incorrect Id');
          return $this->_redirectError(Mage::getUrl('*/*/'));
        }
        if ($info->getCustomerId() != $cid) {
          $this->_getSession()->addError('Cannot edit other customer info');
          return $this->_redirectError(Mage::getUrl('*/*/'));
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
          return $this->_redirectError(Mage::getUrl('*/*/new'));
        }
      }
      $info->setUnit($unit)->setObjectiveCode($oc)->setCostCenter($cc)->save();
      $this->_getSession()->addSuccess($this->__('The billing informations has been saved.'));
      $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
    }
  }
  
  protected function _layout() {
    $this->loadLayout();
    $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
    if ($navigationBlock) {
      $navigationBlock->setActive('icrc/customer_billing');
    }
    $this->_initLayoutMessages('customer/session');
  }
  
  protected function _getSession() {
    return Mage::getSingleton('customer/session');
  }
}


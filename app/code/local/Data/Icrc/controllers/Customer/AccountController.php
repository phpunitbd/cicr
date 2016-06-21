<?php

require_once 'Mage/Customer/controllers/AccountController.php';
class Data_Icrc_Customer_AccountController extends Mage_Customer_AccountController
{
  public function editPostAction()
  {
    if (!$this->_validateFormKey() || !$this->getRequest()->isPost()) {
      return $this->_redirect('*/*/edit');
    }
    parent::editPostAction();
    $CT = $this->getRequest()->getPost('customer-type');
    if (!empty($CT)) Mage::helper('data_icrc/attributes')->setDefaultCustomerTypeValue(null, $CT);
  }

  public function createPostAction()
  {
    $session = $this->_getSession();
    if ($session->isLoggedIn() || !$this->getRequest()->isPost()) {
      $this->_redirect('*/*/');
      return;
    }
    if (Mage::app()->getStore()->getCode() == 'internal') {
      // Test e-mail domain validity
      $email = $this->getRequest()->getPost('email');
      $valid = false;
      foreach (Mage::helper('data_icrc')->getAuthorizedDomains() as $domain) {
        if (preg_match("/@${domain}$/", $email)) {
          $valid = true;
          break;
        }
      }
      if (!$valid) {
        Mage::getSingleton('core/session')->addError('Your e-mail address is not one of the ICRC addresses');
        $this->_redirect('*/*/create');
        return;
      }
    }
    parent::createPostAction();
    $webId = Mage::app()->getStore()->getWebsiteId();
    $customer = Mage::getModel('customer/customer')->setWebsiteId($webId);
    if ($customer->isConfirmationRequired()) {
      // If isConfirmationRequired, then user is not logged in after creation
      // so we must load customer by email to modify it
      $email = $this->getRequest()->getPost('email');
      $user = Mage::getModel('customer/customer')->setWebsiteId($webId)->loadByEmail($email);
      // Let's verify we're handling a new user and not try to modify an already created one
      // Verify password too, won't hurt
      $pwd = $this->getRequest()->getPost('password');
      if ($user->getConfirmation() && $user->validatePassword($pwd))
        $customer = $user;
    }
    else
      $customer = Mage::getSingleton('customer/session');
    if ($customer->getId()) {
      $CT = $this->getRequest()->getPost('customer-type');
      if (!empty($CT)) Mage::helper('data_icrc/attributes')->setDefaultCustomerTypeValue($customer->getId(), $CT);
    }
  }
}


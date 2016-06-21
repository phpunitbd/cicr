<?php

include_once 'Mage/Contacts/controllers/IndexController.php';
class Data_Icrc_ExhibitionController extends Mage_Contacts_IndexController
{
  public function preDispatch() {
    // Don't call Mage_Contacts_IndexController::preDispatch()
    Mage_Core_Controller_Front_Action::preDispatch();
  }
  
  public function getAction() {
    $this->loadLayout();
    $_id = $this->getRequest()->getParam('id');
    $_product = Mage::getModel('catalog/product')->load($_id);
    $this->getLayout()->getBlock('contactForm')
        ->setFormAction(Mage::getUrl('*/*/post'))
        ->setProduct($_product)
        ->setIsExhibition(true);
    $this->_initLayoutMessages('customer/session');
    $this->_initLayoutMessages('catalog/session');
    $this->renderLayout();
  }
  
  public function postAction() {
    parent::postAction();
    $this->_redirect('customer/account/');
  }
  
  public function indexAction() {
    $this->_redirect('contacts/index/');
  }
}


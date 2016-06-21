<?php

require_once 'Mage/Sales/controllers/OrderController.php';
class Data_Icrc_Sales_OrderController extends Mage_Sales_OrderController
{    
    public function reorderAction() {
      parent::reorderAction();
      $changeRedirect = false;
      if ($this->getResponse()->isRedirect()) {
        foreach ($this->getResponse()->getHeaders() as $header) {
          if ($header['name'] == 'Location') {
            if (strstr($header['value'], '/checkout/cart/'))
              $changeRedirect = true;
            break;
          }
        }
      }
      if ($changeRedirect) {
        $from = $this->getRequest()->getParam('orderFrom', base64_encode('customer/account'));
        $this->_redirect(base64_decode($from), array('showCart' => true));
      }
    }
}


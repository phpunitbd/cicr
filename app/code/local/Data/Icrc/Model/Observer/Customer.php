<?php

class Data_Icrc_Model_Observer_Customer
{
  // Event: customer_register_success
  public function onCreate($observer) {
    try {
//$helper = Mage::helper('data_icrc');
//Data_Icrc_Helper_Debug::msg("customerOnCreate Triggered");

      //$customer = Mage::getSingleton('customer/session')->getCustomer();
      //$event=$observer->getEvent();
      //Data_Icrc_Helper_Debug::dump($event);
      $customer = $observer->getCustomer();
      $email = $customer->getEmail();
      $helper = Mage::helper('data_icrc');
      Data_Icrc_Helper_Debug::dump($customer);
      Data_Icrc_Helper_Debug::dump($helper->getAuthorizedDomains());
      foreach ($helper->getAuthorizedDomains() as $domain) {
        if (preg_match("/@${domain}$/", $email)) {
          $customer->setData('group_id', $helper->getICRCGroup());
          // We're not in the save anymore, so need to save manually
          $customer->save();
          break;
        }
      }
    } catch ( Exception $e ) {
      Data_Icrc_Helper_Debug::dump('customer_save_before observer failed: ' . $e->getMessage());
			Mage::log('customer_save_before observer failed: ' . $e->getMessage());
		}
  }

  // Event: sales_model_service_quote_submit_before
  public function onSubmitOrder($observer) {
    $session = Mage::getSingleton('admin/session');
    if ($session->isLoggedIn()) {
      return; // No need to check group as we are in admin area
    }
    
    $helper = Mage::helper('data_icrc');
    //Data_Icrc_Helper_Debug::msg("salesModelService onSubmitOrder Triggered");

    $quote = $observer->getQuote();
    //Data_Icrc_Helper_Debug::dump($quote);
    $customerSession=Mage::getSingleton('customer/session');
    //Data_Icrc_Helper_Debug::dump($customerSession);
    if(!$customerSession->isLoggedIn() && $quote->getCustomerId())
    {
      //Data_Icrc_Helper_Debug::msg("(!$customerSession->isLoggedIn() && $quote->getCustomerId())=true => Register on checkout");
      $customer = $quote->getCustomer();
      $email = $customer->getEmail();
      foreach ($helper->getAuthorizedDomains() as $domain) {
        if (preg_match("/@${domain}$/", $email)) {
          $customer->setData('group_id', $helper->getICRCGroup());
          // We're not in the save anymore, so need to save manually
          //$customer->save();
          break;
        }
      }
    }
  }
  
  public function addressFormat($observer) {
    if (Mage::helper('data_icrc/internal')->isInternal()) {
      //var_dump($observer);
      $type = $observer->getType();
      if ($type && $type->getCode() == 'oneline') {
        $address = $observer->getAddress();
        if ($address) {
          // Test $address->getIcrcType() ??
          $type->setDefaultFormat('{{depend icrc_unit}}({{var icrc_unit}}) {{/depend}}{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}');
        }
      }
    }
  }
}


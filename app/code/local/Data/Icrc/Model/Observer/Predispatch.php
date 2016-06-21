<?php

class Data_Icrc_Model_Observer_Predispatch
{
	//Event: adminhtml_controller_action_predispatch_start
	public function overrideTheme() {
		Mage::getDesign()->setArea('adminhtml')
			->setTheme((string)Mage::getStoreConfig('design/admin/theme'));
  }

	//Event: adminhtml_controller_action_predispatch_start
	public function checkTotp() {
    $session = Mage::getSingleton('core/session');
    $request = Mage::app()->getRequest();
    $token = $request->getParam(Data_Icrc_Helper_Admin::TimeTokenParam);
    if ($token) {
      $valid = Mage::helper('data_icrc/admin')->checkAdminTimeToken($token);
      $session->setData('isTotpValid', $valid);
      if (!$valid) {
        $message = Mage::getSingleton('core/message')->warning('TOTP token is invalid');
        $session->addUniqueMessages($message);
        Data_Icrc_Helper_Debug::dump(debug_backtrace());
      }
    }
    else {
      $valid = $session->getData('isTotpValid', null);
    }
	}
	
	//Event: adminhtml_controller_action_predispatch_start
	public function checkRadar($eventData) {
	  //Data_Icrc_Helper_Debug::dump($eventData);
	  $layout = Mage::getSingleton('core/layout');
	  $request = Mage::app()->getRequest();
	  if ($request->getModuleName() != 'admin')
	    return;
	  Data_Icrc_Helper_Debug::dump($request->getControllerName());
	  if ($request->getControllerName() == 'customer') {
	    $url = Mage::getUrl('icrc/radar/validator');
	    Data_Icrc_Helper_Debug::dump($url);
	    //$layout->getUpdate()->addUpdate('<reference name="head"><action method="addJs"><script>'.$url.'</script></action></reference>');
      //$layout->getUpdate()->load();
      //$layout->generateXml();
      $block = $layout->getBlock('head');
	    Data_Icrc_Helper_Debug::dump($block);
	  }
	}

  // Event: controller_action_predispatch
  public function actionPreDispatch($eventData) {
    // Redirect to login if not login and internal site
    if (Mage::app()->getStore()->getCode() == 'internal') {
      if (!Mage::helper('customer')->isLoggedIn()) {
        $request = $eventData->getControllerAction()->getRequest();
        if ($request->getModuleName() == 'customer'
         && $request->getControllerName() == 'account')
          return;
        if ($request->getModuleName() == 'icrc'
         && $request->getControllerName() == 'payment')
          return;
        Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
        $eventData->getControllerAction()->setFlag('', 'no-dispatch', true);
      }
    }
  }
}


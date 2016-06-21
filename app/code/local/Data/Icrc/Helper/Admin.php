<?php

class Data_Icrc_Helper_Admin extends Mage_Core_Helper_Abstract
{
  const IcrcAdminRole = 'ICRC Administrators';
  public function changeICRCAdminRole($permissions) {
    $roles = Mage::getModel('admin/roles')->getCollection();
    foreach ($roles as $role) {
      if ($role->getRoleName() == self::IcrcAdminRole) {
        $admin = $role;
        break;
      }
      if ($role->getRoleName() == 'ICRC Administrator') {
        $admin = Mage::getModel('admin/roles')->load($role->getId());
        $admin->setName(self::IcrcAdminRole)->save();
        break;
      }
    }

    if (!isset($admin)) {
      $admin = Mage::getModel('admin/roles');
      $admin->setName(self::IcrcAdminRole)->setRoleType('G')->setPid(false)->save();
    }
    Mage::getModel('admin/rules')->setRoleId($admin->getId())->setResources($permissions)->saveRel();
  }

  const TimeTokenParam = 'admin_totp_token';
  const key = 'Ec5bucedkoatIpIrcegos05BlottEpUr';
  public function checkAdminTimeToken($token = null) {
    if ($token === null)
      $token = Mage::app()->getRequest()->getParam(self::TimeTokenParam);
    if (strpos($token, ':') === FALSE) return false;
    list($time, $hash) = explode(':', $token);
    $computed = hash_hmac("sha1" , $time, self::key);
    if ($computed == $hash) {
      $now = time();
      return ($time < $now + 5 && $time > $now - 30);
    }
    else
      return false;
  }
  
  public function radarValidatorLoad() {
    $request = Mage::app()->getRequest();
    $url = null;
	  Data_Icrc_Helper_Debug::dump($request->getControllerName());
	  if ($request->getControllerName() == 'customer') {
	    $url = Mage::getUrl('icrc/radar/validator', array('admin' => 'validator'));
    }
    if ($request->getControllerName() == 'sales_order_create') {
	    $url = Mage::getUrl('icrc/radar/validator', array('admin' => 'validator'));
    }
    return $url;
  }
}


<?php

class Data_Icrc_Model_Session extends Mage_Core_Model_Session
{
  /**
   * Init session
   *
   * @param string $namespace
   * @param string $sessionName
   * @return Mage_Core_Model_Session_Abstract
   */
  public function init($namespace, $sessionName = null)
  {
    if ($namespace == 'core' && 
        $sessionName == Mage_Core_Controller_Front_Action::SESSION_NAMESPACE &&
        array_key_exists('MAGE_RUN_CODE', $_SERVER) && 
        $_SERVER['MAGE_RUN_CODE'] == 'internal') {
      $sessionName = 'internalfrontend';
    }
    parent::init($namespace, $sessionName);
    return $this;
  }
}


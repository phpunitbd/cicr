<?php

class Data_Icrc_Helper_Debug extends Mage_Core_Helper_Abstract
{
  private static $dir;
  private static $file;
  private static $isDebug;

  static function dump($obj, $append = true, $file = null) {
    if (!isset(self::$isDebug))
      self::_init();
    if (self::$isDebug) {
      self::msg(Zend_Debug::dump($obj, null, false), $append, $file, false);
    }
  }

  static function msgdump($msg, $obj, $append = true, $file = null) {
    if (!isset(self::$isDebug))
      self::_init();
    if (self::$isDebug) {
      self::msg($msg . Zend_Debug::dump($obj, null, false), $append, $file, false);
    }
  }

  static function message($msg, $append = true, $file = null, $nl = true) {
    self::msg($msg, $append, $file, $nl);
  }
  
  static function msg($msg, $append = true, $file = null, $nl = true) {
    if (!isset(self::$isDebug))
      self::_init();
    if (self::$isDebug) {
      $_file = $file === null ? self::$file : self::$dir . DS . basename($file);
      if (!$append) {
        $fp = fopen($_file, 'w');
        fclose($fp);
      }
      error_log($msg . ($nl ? "<br/>\n" : ''), 3, $_file);
    }
  }
  
  static function isDebug() {
    if (!isset(self::$isDebug))
      self::_init();
    return self::$isDebug;
  }
  
  private static function _init() {
    self::$dir = Mage::getBaseDir('media') . DS . 'debug';
    self::$isDebug = file_exists(self::$dir) && is_dir(self::$dir);
    self::$file = self::$dir . DS . 'log.html';
  }
}

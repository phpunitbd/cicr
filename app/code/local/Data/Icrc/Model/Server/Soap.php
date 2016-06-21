<?php

class Data_Icrc_Model_Server_Soap extends Zend_Soap_Server
{
  protected function _setRequest($request)
  {
    if (is_string($request)) {
      $dom = new DOMDocument();
      if(strlen($request) == 0 || !$dom->loadXML($request, LIBXML_PARSEHUGE)) {
        throw new Zend_Soap_Server_Exception('Invalid XML');
      }
      $request = $dom;
    }
    return parent::_setRequest($request);
  }
}

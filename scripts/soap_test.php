<?php

define('base_url', 'http://icrc-local-dev.int.data.fr/index.php/api/v2_soap/');

$client = new SoapClient(base_url . '?wsdl=1');
try {
//$session = $client->login('root', 'rootroot');
$session = $client->login(array('username' => 'root', 'apiKey' => 'rootroot'));
//$session = $client->__call('login', array('username' => 'root', 'apiKey' => 'rootroot'));
//var_dump($session);
$id = $session->result;
} catch (Exception $e) {
  var_dump($e);
}

if (!empty($_SERVER['KEY'])) {
  $loc = $client->__setLocation(base_url . '?XDEBUG_SESSION_START=ECLIPSE_DBGP&KEY=' . $_SERVER['KEY']);
}
/*
try {
echo "getInvoice.\n";
$res = $client->fileGetInvoice(array('sessionId' => $id, 'invoiceId' => '4'));
$fp = fopen('/tmp/result.pdf', 'w');
fwrite($fp, base64_decode($res->result));
fclose($fp);
} catch (Exception $e) {
  var_dump($e);
}
*/
try {
echo "getInvoice.\n";
$res = $client->fileGetInvoice(array('sessionId' => $id, 'invoiceId' => '25'));
$fp = fopen('/tmp/result2.pdf', 'w');
fwrite($fp, base64_decode($res->result));
fclose($fp);
} catch (Exception $e) {
  var_dump($e);
}

$client->__setLocation();

$client->_cookies = array();
$res = $client->endSession(array('sessionId' => $id));


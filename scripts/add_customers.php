<?php

require_once('app/Mage.php');
Mage::app();

for ($i = 2; $i < 600; $i++) {
  $m = Mage::getModel('customer/customer');
  if ($i % 4 == 0)
    $m->setMiddlename('F.');
  if ($i % 9 == 0)
    $m->setSuffix('Jr.');
  if ($i % 12 == 0)
    $m->setPrefix('Dr.');
  $m->setFirstname('toto'.$i)
    ->setLastname('titi'.$i)
    ->setEmail('toto.titi.'.$i.'@dev.nu.ll')
    ->save();
}


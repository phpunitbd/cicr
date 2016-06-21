<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
$blocks = Mage::getModel('cms/block')->getCollection();

$slides = array('index_slide_1', 'index_slide_2', 'index_slide_3', 'index_slide_4');

foreach ($blocks as $block) {
  if (in_array($block->getIdentifier(), $slides)) {
    $tmp = Mage::getModel('cms/block')->load($block->getId());
    $sid = $tmp->getStoreId();
    if ($sid[0] == $fr) {
      $tmp->setContent("<h1>L&rsquo;eau et l&rsquo;habitat, favoriser des conditions de vie ad&eacute;quates</h1>
<div class=\"content\">Epid&eacute;mies, urbanisation, insalubrit&eacute;, qualit&eacute; d'eau, d&eacute;gradation des infrastructures : des menaces directes sur la sant&eacute; et la dignit&eacute; des populations touch&eacute;es par des conflits arm&eacute;s dans le monde. Autant de crit&egrave;res d'intervention pour l'unit&eacute; eau et habitat du CICR, qui pr&eacute;sente dans cette brochure les multiples facettes de ses activit&eacute;s, en situation d'urgence, de crise chronique et de redressement suite &agrave; une crise.</div>
<p>{{block type=\"core/template\" template=\"catalog/product/home.phtml\" product=\"V-F-CR-F-01084-V,0969\" size=\"150,135\"}}</p>");
      $tmp->save();
    } else {
      $tmp->setContent("<h1>ICRC's Campaign: &ldquo;Health Care in Danger&rdquo;</h1>
<div class=\"content\">The Health Care in Danger campaigns is an ICRC-led, Red Cross and Red Crescent Movement-wide initiative that aims to address the widespread and severe impact of illegal and sometimes violent acts that obstruct the delivery of health care, damage or destroy facilities and vehicules, and injure or kill health-care workers and patients, in armed conflicts and other emergencies.</div>
<p>{{block type=\"core/template\" template=\"catalog/product/home.phtml\" product=\"4074,V-F-CR-F-00834-B\" size=\"135,150\"}}</p>");
      $tmp->save();
    }
  }
}

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('icrc/web/welcome', 'Shop', 'default', 0);
$conf->saveConfig('icrc/web/submessage', 'of the ICRC', 'default', 0);
$conf->saveConfig('icrc/web/welcome', 'Boutique', 'stores', $fr);
$conf->saveConfig('icrc/web/submessage', 'du comité international de la croix rouge', 'stores', $fr);
$conf->saveConfig('icrc/web/welcome', 'Internal Shop', 'stores', $int);
$conf->saveConfig('icrc/web/footer_logo', 'images/Logo-CICR-footer.png', 'stores', $fr);
$conf->saveConfig('design/footer/copyright', '<span class="copyright">&copy;</span> International Committee of the Red Cross', 'default', 0);
$conf->saveConfig('design/footer/copyright', '<span class="copyright">&copy;</span> Comit&eacute; international de la croix-rouge', 'stores', $fr);

$this->endSetup();


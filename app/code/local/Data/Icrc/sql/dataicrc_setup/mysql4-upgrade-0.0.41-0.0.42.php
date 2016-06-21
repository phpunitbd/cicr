<?php

$this->startSetup();

function createBlock_42($title, $store, $id, $cnt = null)
{
  try {
    $staticBlock = array(
                'title' => $title,
                'identifier' => $id,
                'content' => $cnt ? $cnt : 'Sample data for block '.$title,
                'is_active' => 1,
                'stores' => array($store)
                );
 
    Mage::getModel('cms/block')->setData($staticBlock)->save();
  }
  catch (Mage_Core_Exception $e) {
    // ignore
  }
}

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$fr_txt = '<ul>
<li><a href="reference-publications-medical.html">M&eacute;dical</a></li>
<li><a href="reference-publications-law.html">Juridique</a></li>
<li><a href="reference-publications-humanitarian.html">Humanitaire</a></li>
<li><a href="reference-publications-academic.html">Acad&eacute;mique</a></li>
<li><a href="reference-publications-military.html">Militaire</a></li>
</ul>';
$en_txt = '<ul>
<li><a href="reference-publications-medical.html">Medical</a></li>
<li><a href="reference-publications-law.html">Law</a></li>
<li><a href="reference-publications-humanitarian.html">Humanitarian</a></li>
<li><a href="reference-publications-academic.html">Academic</a></li>
<li><a href="reference-publications-military.html">Military</a></li>
</ul>';

createBlock_42("Reference Publications for EN", $en, "ref_pub", $en_txt);
createBlock_42("Reference Publications for Internal", $int, "ref_pub", $en_txt);
createBlock_42("Reference Publications for FR", $fr, "ref_pub", $fr_txt);

$this->endSetup();


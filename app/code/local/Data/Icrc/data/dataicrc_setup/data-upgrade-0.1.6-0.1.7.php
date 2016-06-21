<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();
Mage::register('isSecureArea', true);


$cats = Mage::getModel('catalog/category')->getCollection();
foreach ($cats as $cs) {
  $c = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId(0)->load($cs->getId());
  if (strcasecmp($c->getName(), 'Gifts') == 0) {
    $cat = Mage::getModel('data_icrc/catalog_categoryeav')->setStoreId($fr)->load($c->getId());
    if ($cat->getName() == "Articles promotionnels") continue;
    $cat->setName("Articles promotionnels")->save();
    continue;
  }
}

$global_to_store = array('add_material', 'additional_comments', 'dimensions', 'brand_name');
foreach ($global_to_store as $attr) {
  $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attr);
  $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
  $attribute->setIsGlobal(0)->save();
}

$p404en = '<h1>Page not found</h1>
<h2>The page you requested was not found</h2>
<ul class="disc">
<li>If you typed the URL directly, please make sure the spelling is correct.</li>
<li>If you clicked on a link to get here, the link is outdated.</li>
</ul>
<h2>What can you do?</h2>
<ul class="disc">
<li><a onclick="history.go(-1); return false;" href="#">Go back</a> to the previous page.</li>
<li>Use the search bar at the top of the page to search for your products.</li>
<li>Or follow these links<br /><a href="{{store url=""}}">Store Home</a> <span class="separator">|</span> <a href="{{store url="customer/account"}}">My Account</a></li>
</ul>';
$p404fr = '<h1>Page non trouvée</h1>
<h2>La page que vous cherchiez n\'existe pas</h2>
<ul class="disc">
<li>Si vous avez entré l\'adresse directement, vérifiez ne pas avoir fait d\'erreur.</li>
<li>Si vous avez cliqué sur un lien, il est probablement périmé.</li>
</ul>
<h2>Que pouvez-vous faire ?</h2>
<ul class="disc">
<li><a onclick="history.go(-1); return false;" href="#">Retourner</a> à la page précédente.</li>
<li>Utiliser le champ de recherche situé au dessus pour rechercher votre produit.</li>
<li>Ou suivre un de ces liens :<br /><a href="{{store url=""}}">Accueil</a> <span class="separator">|</span> <a href="{{store url="customer/account"}}">Mon compte</a></li>
</ul>';

Mage::helper('data_icrc/update')->createCmsPage('404 Error', array($en, $int), 'no-route', $p404en);
Mage::helper('data_icrc/update')->createCmsPage('Erreur 404', $fr, 'no-route', $p404fr);

// Do not forget to unset ...
Mage::unregister('isSecureArea');

$this->endSetup();



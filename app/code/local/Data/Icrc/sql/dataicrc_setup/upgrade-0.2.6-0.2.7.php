<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

function createBlock_2_7($title, $store, $id, $cnt = null)
{
  try {
    $stores = is_array($store) ? $store : array($store);
    $staticBlock = array(
                'title' => $title,
                'identifier' => $id,
                'content' => $cnt ? $cnt : 'Sample data for block '.$title,
                'is_active' => 1,
                'stores' => $stores
                );
 
    Mage::getModel('cms/block')->setData($staticBlock)->save();
  }
  catch (Mage_Core_Exception $e) {
    // ignore
  }
}

/* Get ID's for categories */
$categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name');
foreach ($categories as $category) {
  switch ($category->getName()) {
    case 'Publications': $pub_id = $category->getId(); break;
    case 'Films': $film_id = $category->getId(); break;
    case 'Gifts': $gift_id = $category->getId(); break;
    case 'E-Books': $ebook_id = $category->getId(); break;
    case 'Exhibitions': $exhibition_id = $category->getId(); break;
  }
}

/* Get ID's for CMS pages */
$pages = Mage::getModel('cms/page')->getCollection()->addStoreFilter($en);
foreach ($pages as $page) {
  switch ($page->getIdentifier()) {
    case 'shipping-policy': $shipping_policy_en = $page->getPageId(); break;
    case 'exchange': $exchange_en = $page->getPageId(); break;
    case 'legal-notices': $legal_notice_en = $page->getPageId(); break;
    case 'pricing-policy': $pricing_policy_en = $page->getPageId(); break;
    case 'customer-service': $customer_service_en = $page->getPageId(); break;
    case 'payment-policy-security': $pricing_policy_security_en = $page->getPageId(); break;
  }
}
$pages = Mage::getModel('cms/page')->getCollection()->addStoreFilter($fr);
foreach ($pages as $page) {
  switch ($page->getIdentifier()) {
    case 'shipping-policy': $shipping_policy_fr = $page->getPageId(); break;
    case 'exchange': $exchange_fr = $page->getPageId(); break;
    case 'legal-notices': $legal_notice_fr = $page->getPageId(); break;
    case 'pricing-policy': $pricing_policy_fr = $page->getPageId(); break;
    case 'customer-service': $customer_service_fr = $page->getPageId(); break;
    case 'payment-policy-security': $pricing_policy_security_fr = $page->getPageId(); break;
  }
}

$en_txt = '<ul>
  <li class="first">&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$shipping_policy_en.'"}}</li>
  <li>&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$exchange_en.'"}}</li>
  <li>&gt; <a href="{{store url="contacts"}}">Contact</a></li>
  <li>&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$legal_notice_en.'"}}</li>
  <li class=" last">&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$pricing_policy_security_en.'"}}</li>
</ul>';
$fr_txt = '<ul>
  <li class="first">&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$shipping_policy_fr.'"}}</li>
  <li>&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$exchange_fr.'"}}</li>
  <li>&gt; <a href="{{store url="contacts"}}">Contactez nous</a></li>
  <li>&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$legal_notice_fr.'"}}</li>
  <li class=" last">&gt; {{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$pricing_policy_security_fr.'"}}</li>
</ul>';
createBlock_2_7("Customer Service for EN", array($en, $int), 'customer_service_side', $en_txt);
createBlock_2_7("Customer Service for FR", $fr, 'customer_service_side', $fr_txt);

$categories = '<ul class="links category">
<li class="first">{{widget type="catalog/category_widget_link" template="catalog/category/widget/link/link_block.phtml" id_path="category/'.$pub_id.'"}}</li>
<li>{{widget type="catalog/category_widget_link" template="catalog/category/widget/link/link_block.phtml" id_path="category/'.$film_id.'"}}</li>
<li>{{widget type="catalog/category_widget_link" template="catalog/category/widget/link/link_block.phtml" id_path="category/'.$gift_id.'"}}</li>
<li>{{widget type="catalog/category_widget_link" template="catalog/category/widget/link/link_block.phtml" id_path="category/'.$ebook_id.'"}}</li>
<li class="last">{{widget type="catalog/category_widget_link" template="catalog/category/widget/link/link_block.phtml" id_path="category/'.$exhibition_id.'"}}</li>
</ul>';
$en_inner_txt = '<ul class="links">
    <li class="first">{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$pricing_policy_en.'"}}</li>
    <li>{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$legal_notice_en.'"}}</li>
    <li>{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$shipping_policy_en.'"}}</li>
    <li>{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$customer_service_en.'"}}</li>
    <li class=" last">{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$pricing_policy_security_en.'"}}</li>
  </ul>
'.$categories.'
</div>
<div class="left">
  <img src="{{skin url="images/Logo-ICRC-footer.png"}}" class="logo" alt="logo">
  <span class="copyright">©</span> International Committee of the Red Cross
</div>';
$en_txt = '<div class="right">
  <img src="{{skin url="images/payment-methods-footer.jpg"}}" class="payment-methods" alt="payment">'.
  $en_inner_txt;
$int_txt = '<div class="right">
  <img src="{{skin url="images/empty.png"}}" class="payment-methods" alt="">'.
  $en_inner_txt;
$fr_txt = '<div class="right">
  <img src="{{skin url="images/payment-methods-footer.jpg"}}" class="payment-methods" alt="payment">
  <ul class="links">
    <li class="first">{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$pricing_policy_fr.'"}}</li>
    <li>{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$legal_notice_fr.'"}}</li>
    <li>{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$shipping_policy_fr.'"}}</li>
    <li>{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$customer_service_fr.'"}}</li>
    <li class=" last">{{widget type="cms/widget_page_link" template="cms/widget/link/link_inline.phtml" page_id="'.$pricing_policy_security_fr.'"}}</li>
  </ul>
'.$categories.'
</div>
<div class="left">
  <img src="{{skin url="images/Logo-ICRC-footer.png"}}" class="logo" alt="logo">
  <span class="copyright">©</span> Comité international de la croix-rouge
</div>';
createBlock_2_7("Footer for EN", $en, 'page_footer', $en_txt);
createBlock_2_7("Footer for Internal", $int, 'page_footer', $int_txt);
createBlock_2_7("Footer for FR", $fr, 'page_footer', $fr_txt);

$en_txt = '<div id="msg_co_connected">
  <div class="page-title">
    <h1>Your order has been received.</h1>
  </div>
  <h2 class="sub-title">Thank you for your purchase!</h2>
  <p>Your order # is: <a href="{{var view_order_id}}">{{var order_id}}</a>.</p>
  <p>You will receive an order confirmation email with details of your order and a link to track its progress.</p>
  <p>Click <a href="{{var print_url}}">here to print</a> a copy of your order confirmation.</p>
  <div class="buttons-set">
    <button type="button" class="button" title="Continue Shopping" onclick="window.location=\'{{store url=""}}\'"><span><span>Continue Shopping</span></span></button>
  </div>
</div>
<div id="msg_co_not_connected">
  <div class="page-title">
    <h1>Your order has been received.</h1>
  </div>
  <h2 class="sub-title">Thank you for your purchase!</h2>
  <p>Your order # is: {{var order_id}}.</p>
  <p>You will receive an order confirmation email with details of your order and a link to track its progress.</p>
  <div class="buttons-set">
    <button type="button" class="button" title="Continue Shopping" onclick="window.location=\'{{store url=""}}\'"><span><span>Continue Shopping</span></span></button>
  </div>
</div>';
$fr_txt = '<div id="msg_co_connected">
  <div class="page-title">
    <h1>Votre commande a bien été validée.</h1>
  </div>
  <h2 class="sub-title">Nous vous remercions pour votre achat !</h2>
  <p>Votre n⁰ de commande est <a href="{{var view_order_id}}">{{var order_id}}</a>.</p>
  <p>Vous recevrez un e-mail de confirmation avec tous les details concernant votre commande. Pour toute question, n&apos;hésitez pas à nous contacter à shop@icrc.org. Merci de votre confiance et à bientôt l&apos;équipe CICR E-commerce. </p>
  <p>Cliquez <a href="{{var print_url}}">ici pour imprimer</a> une copie de votre confirmation de commande.</p>
  <div class="buttons-set">
    <button type="button" class="button" title="Poursuivre mes achats" onclick="window.location=\'{{store url=""}}\'"><span><span>Poursuivre mes achats</span></span></button>
  </div>
</div>
<div id="msg_co_not_connected">
  <div class="page-title">
    <h1>Votre commande a bien été validée.</h1>
  </div>
  <h2 class="sub-title">Nous vous remercions pour votre achat !</h2>
  <p>Votre n⁰ de commande est {{var order_id}}.</p>
  <p>Vous recevrez un e-mail de confirmation avec tous les details concernant votre commande. Pour toute question, n&apos;hésitez pas à nous contacter à shop@icrc.org. Merci de votre confiance et à bientôt l&apos;équipe CICR E-commerce. </p>
  <div class="buttons-set">
    <button type="button" class="button" title="Poursuivre mes achats" onclick="window.location=\'{{store url=""}}\'"><span><span>Poursuivre mes achats</span></span></button>
  </div>
</div>';
$js = '<script type="text/javascript">// <![CDATA[
if ("{{var can_view_order}}" != false) $("msg_co_not_connected").hide();
else  $("msg_co_connected").hide();
// ]]></script>';
createBlock_2_7("Checkout Success for EN", array($en, $int), 'checkout_success', $en_txt.$js);
createBlock_2_7("Checkout Success for FR", $fr, 'checkout_success', $fr_txt.$js);

$en_txt = '<div class="page-title">
  <h1>An error occurred in the process of payment</h1>
</div>
<p>Order #{{var order.increment_id}}</p>
<p>{{var error_message}}</p>
<p>Click <a href="{{store url=""}}">here</a> to continue shopping.</p>';
$fr_txt = '<div class="page-title">
  <h1>Une erreur est survenue lors du paiement</h1>
</div>
<p>N⁰ de commande {{var order.increment_id}}</p>
<p>{{var error_message}}</p>
<p><a href="{{store url=""}}">Poursuivre mes achats</a>.</p>';
createBlock_2_7("Checkout Error for EN", array($en, $int), 'checkout_failure', $en_txt);
createBlock_2_7("Checkout Error for FR", $fr, 'checkout_failure', $fr_txt);


$this->endSetup();


<?php

class Data_Icrc_Block_Webtrends extends Mage_Core_Block_Abstract
{
  protected $meta = array();

  protected function _toHtml() {
    $html = "<!-- webtrends -->\n";
    foreach ($this->meta as $name => $meta) {
      if (is_array($meta)) {
        $cnt = array();
        foreach ($meta as $item)
          $cnt[] = $item->getContent();
        $html .= '<meta name="' . $name . '" content="' . implode($cnt, ',') . '" />' . "\n";
      } else {
        $html .= '<meta name="' . $meta->getName() . '" content="' . $meta->getContent() . '" />' . "\n";
      }
    }
    return $html;
  }

  protected function _beforeToHtml() {
    // Try to load product block
    $product = $this->getLayout()->getBlock('product.info');
    if ($product && $product->getProduct())
      $this->addProduct($product->getProduct());
  }
  
  protected function addProduct($product) {
    $sku = new Varien_Object();
    $sku->setName('WT.pn_sku')
        ->setcontent($product->getSku());
    $this->add($sku);
    $famille = new Varien_Object();
    $famille->setName('WT.pn_fa')
            ->setcontent(Mage::helper('data_icrc/product')->getAttributeSetLabel($product));
    $this->add($famille);
    $cats = $product->getCategoryIds();
    foreach ($cats as $category_id) {
      $_cat = Mage::getModel('catalog/category')->load($category_id) ;
      if ($_cat->getLevel() == 1) { // Main category
        $groupe = new Varien_Object();
        $groupe->setName('WT.pn_gr')
               ->setcontent($_cat->getName());
        $this->add($groupe);
      }
      if ($_cat->getLevel() == 2) { // Sub - category
        $sousCategorie = new Varien_Object();
        $sousCategorie->setName('WT.pn_sc')
                      ->setcontent($_cat->getName());
        $this->add($sousCategorie);
      }
    }
  }
  
  protected function add($meta, $replace = false) {
    $name = $meta->getName();
    if ($replace) {
      $this->meta[$name] = $meta;
    } else {
      if (array_key_exists($name, $this->meta)) {
        $old = is_array($this->meta[$name]) ?
               $this->meta[$name] : 
               array($this->meta[$name]);
        $this->meta[$name] = array_merge($old, array($meta));
      } else {
        $this->meta[$name] = $meta;
      }
    }
  }
}

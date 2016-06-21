<?php

class Data_Icrc_Model_Currency extends Mage_Directory_Model_Currency
{
  /**
   * Apply currency format to number with specific rounding precision
   *
   * @param   float $price
   * @param   int $precision
   * @param   array $options
   * @param   bool $includeContainer
   * @param   bool $addBrackets
   * @return  string
   */
  public function formatPrecision($price, $precision, $options=array(), $includeContainer = true, $addBrackets = false)
  {
    if (!isset($options['precision'])) {
      $options['precision'] = $precision;
    }
    if ($includeContainer) {
	    $priceTemp='<span class="price icrc-price">' . ($addBrackets ? '[' : '') . $this->formatTxt($price, $options) . ($addBrackets ? ']' : '') . '</span>';
      return preg_replace('/([0-9]+)([\., 0-9]+)*([\.,][0-9]+.*)/','$1$2<span class="sup">$3</span>', $priceTemp);
    }

    return $this->formatTxt($price, $options);
  }

  /**
   * Save currency rates
   *
   * @param array $rates
   * @return object
   */
  public function saveRates($rates)
  {
    Data_Icrc_Helper_Debug::dump($rates);
    parent::saveRates($rates);
    $category = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'donations');
    if ($category) {
      $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
      $baseCurrencyCodePrefix = 'donation-' . $baseCurrencyCode . '-';
      $baseCurrencyCodePrefixLen = strlen($baseCurrencyCodePrefix);
      $donations = Mage::getResourceModel('catalog/product_collection')
                           ->addCategoryFilter($category);
      list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
      foreach ($donations as $p) {
        Mage::getModel('catalog/product')->setStoreId($en)->load($p->getId())->setPrice(false)->save();
        Mage::getModel('catalog/product')->setStoreId($fr)->load($p->getId())->setPrice(false)->save();
        if (strncmp($p->getSku(), $baseCurrencyCodePrefix, $baseCurrencyCodePrefixLen) == 0)
          continue;
        $pp = Mage::getModel('catalog/product')->setStoreId(0)->load($p->getId());
        $cost = $pp->getProductionCost();
        if (is_null($cost))
          $cost = Mage::helper('data_icrc')->findDonationCurrencyValue($pp);
        $code = Mage::helper('data_icrc')->findDonationCurrencyCode($pp);
        if ($cost && $code && isset($rates[$baseCurrencyCode][$code])) {
          $rate = $rates[$baseCurrencyCode][$code];
          $price = (int)$cost / $rate;
          Data_Icrc_Helper_Debug::msg("cost: $cost ; rate: $rate => price: $price");
          $pp->setPrice($price)->save();
        }
      }
    }
    return $this;
  }

  // This one as a huge bug
  public function convert($price, $toCurrency=null)
  {
    if (is_null($toCurrency)) {
        return $price;
    }
    elseif ($rate = $this->getRate($toCurrency)) {
        return $price*$rate;
    }

    throw new Exception(Mage::helper('directory')->__('Undefined rate from "%s-%s".', $this->getCode(), $toCurrency));
  }
}


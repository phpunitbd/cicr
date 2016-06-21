<?php

class Data_Icrc_Helper_Data extends Mage_Core_Helper_Abstract
{
  function getCartItemCount() {
    return $this->getItemCount(Mage::getSingleton('checkout/cart'));
  }

  function getItemCount($cart) {
    $qty = $cart->getSummaryQty();
    switch ($qty) {
      case 0:
        return $response['items'] = $this->__('No item');
      case 1:
        return $response['items'] = $this->__('%d item', $qty);
      default:
        return $response['items'] = $this->__('%d items', $qty);
    }
  }
  
  function welcomeMessage() {
    $user = Mage::getSingleton('customer/session')->getCustomer();
    $prefix = $this->__($user->getPrefix());
    return $this->__('Welcome<br/>%s', $user->getFirstname());
  }

  function getImageRatio($product, $size = 75) {
    list ($url) = $this->getImageRatioWithInfo($product, $size);
    return $url;
  }

  /**
    * Returns (URL, margin-top, margin-right, margin-bottom, margin-left, width, height)
    */
  function getImageRatioWithInfo($product, $size) {
    return $this->_getImageTypeRatioWithInfo($product, 'image', $size);
  }

  function getThumbnailRatio($product, $size = 75) {
    list ($url) = $this->getThumbnailRatioWithInfo($product, $size);
    return $url;
  }

  /**
    * Returns (URL, margin-top, margin-right, margin-bottom, margin-left, width, height)
    */
  function getThumbnailRatioWithInfo($product, $size) {
    return $this->_getImageTypeRatioWithInfo($product, 'thumbnail', $size);
  }

  protected function _getImageTypeRatioWithInfo($product, $type, $size) {
    $image = Mage::helper('catalog/image')->init($product, $type);
    $originalWidth = $image->getOriginalWidth();
    $originalHeight = $image->getOriginalHeigh();
    $top = 0; $right = 0; $bottom = 0; $left = 0;
    if ($originalWidth == $originalHeight) {
      $width = $size;
      $height = $size;
    }
    elseif ($originalWidth >= $originalHeight) {
      $width = $size;
      $height = round($size * $originalHeight / $originalWidth);
      $top = floor((abs($size - $height) / 2));
      $bottom = $size - $height - $top;
    }
    else {
      $height = $size;
      $width = round($size * $originalWidth / $originalHeight);
      $left = floor((abs($size - $width) / 2));
      $right = $size - $width - $left;
    }
    $imageurl = (string)$image->resize($width, $height);
    return array($imageurl, $top, $right, $bottom, $left, $width, $height);
  }

  function getImageRatioWithSquareHtml($product, $size, $square_size = 5) {
    list ($imageurl, $top, $right, $bottom, $left, $width, $height) = $this->_getImageTypeRatioWithInfo($product, 'small_image', $size);
    $margin = $margin = "${top}px ${right}px ${bottom}px ${left}px";
    $label = $product->getData('image_label');
    if (empty($label)) $label = $product->getName();
    $name = Mage::helper('core')->stripTags($label, null, true);
    $img = "<img src=\"$imageurl\" style=\"margin: $margin;\" alt=\"$name\" width=\"$width\" height=\"$height\" />";
    $tls = $size + $square_size;
    $brs = $size;
    return "<div class=\"product-square-part square-top-left square-size-$square_size\" style=\"height: ${tls}px; width: ${tls}px;\"><div class=\"product-square-part square-bottom-right square-size-$square_size\" style=\"height: ${brs}px; width: ${brs}px;\">$img</div></div>";
  }

  function getImageRatioWithFixedWidthHtml($product, $size, $link = true, $type = 'image') {
    list ($imageurl, $top, $right, $bottom, $left, $width, $height) = $this->_getImageTypeRatioWithInfoWidth($product, $type, $size);
    $label = $product->getData('image_label');
    if (empty($label)) $label = $product->getName();
    $name = Mage::helper('core')->stripTags($label, null, true);
    $img = "<img src=\"$imageurl\" alt=\"$name\" title=\"$name\" width=\"$width\" height=\"$height\" />";
    if ($link)
      return '<a href="' . $product->getProductUrl() . '">' . $img . '</a>';
    else
      return $img;
  }

  function getThumbnailRatioWithInfoWidth($product, $size) {
    return $this->_getImageTypeRatioWithInfoWidth($product, 'thumbnail', $size);
  }

  function _getImageTypeRatioWithInfoWidth($product, $type, $size) {
    $image = Mage::helper('catalog/image')->init($product, $type);
    $originalWidth = $image->getOriginalWidth();
    $originalHeight = $image->getOriginalHeigh();
    $top = 0; $right = 0; $bottom = 0; $left = 0;
    if ($originalWidth == $size) {
      $width = $size;
      $height = $originalHeight;
    }
    else {
      $width = $size;
      $height = round($size * $originalHeight / $originalWidth);
    }
    $imageurl = (string)$image->resize($width, $height);
    return array($imageurl, $top, $right, $bottom, $left, $width, $height);
  }

  public function purgeAccountNavigationLinks($array) {
    unset($array['billing_agreements']);
    unset($array['recurring_profiles']);
    unset($array['reviews']);
    unset($array['tags']);
    unset($array['OAuth Customer Tokens']);
    unset($array['downloadable_products']);
    if (Mage::helper('data_icrc/internal')->isInternal()) {
      //var_dump($array);
      $array['internal_payment_info'] = new Varien_Object(
        array(
          'name' => 'account',
          'path' => 'icrc/customer_billing',
          'label' => 'Billing Informations',
          'url' => Mage::getUrl('icrc/customer_billing')
        )
      );
    }
    return $array;
  }

  public function getAuthorizedDomains() {
    if (Mage::getStoreConfig('icrc/janus/enable') == 0) // If Janus is disabled (=dev), allow data.fr
      return array('icrc.org', 'data.fr');
    return array('icrc.org');
  }

  private $ICRCGroup = null;
  public function getICRCGroup() {
    if ($this->ICRCGroup === null) {
      $customer_groups = Mage::getModel('customer/group')->getCollection();
      foreach ($customer_groups as $group) {
        if ($group->getCode() == 'ICRC') {
          $this->ICRCGroup = $group->getId();
          break;
        }
      }
    }
    return $this->ICRCGroup;
  }

  public function findDonationCurrencyValue(&$product) {
    $sku = $product->getSku();
    $matches = array();
    if (preg_match('/^donation-[A-Z]{2,3}-([0-9]+)$/', $sku, $matches)) {
      $value = (int)$matches[1];
      $product->setProductionCost($value);
      return $value;
    }
    return 0;
  }

  public function findDonationCurrencyCode(&$product) {
    $sku = $product->getSku();
    $matches = array();
    if (preg_match('/^donation-([A-Z]{2,3})-[0-9]+$/', $sku, $matches)) {
      return $matches[1];
    }
    return null;
  }
  
  public function getRoute() {
    $request = Mage::app()->getRequest();
    $route = $request->getModuleName() . '/' . $request->getControllerName();
    if ($request->getActionName()) 
      $route .= '/' . $request->getActionName();
    return base64_encode($route);
  }
  
  static function sortLayerItemsOnLabel(Mage_Catalog_Model_Layer_Filter_Item $a, Mage_Catalog_Model_Layer_Filter_Item $b) {
    return strnatcasecmp($a->getLabel(), $b->getLabel());
  }

  public function getCountryDir() {
    return array(
      'Afghanistan' => 'AF',
      'Albania' => 'AL',
      'Algeria' => 'DZ',
      'American Samoa' => 'AS',
      'Andorra' => 'AD',
      'Angola' => 'AO',
      'Anguilla' => 'AI',
      'Antarctica' => 'AQ',
      'Antigua and Barbuda' => 'AG',
      'Argentina' => 'AR',
      'Armenia' => 'AM',
      'Aruba' => 'AW',
      'Australia' => 'AU',
      'Austria' => 'AT',
      'Azerbaijan' => 'AZ',
      'Bahamas' => 'BS',
      'Bahrain' => 'BH',
      'Bangladesh' => 'BD',
      'Barbados' => 'BB',
      'Belarus' => 'BY',
      'Belgium' => 'BE',
      'Belize' => 'BZ',
      'Benin' => 'BJ',
      'Bermuda' => 'BM',
      'Bhutan' => 'BT',
      'Bolivia' => 'BO',
      'Bosnia and Herzegovina' => 'BA',
      'Botswana' => 'BW',
      'Bouvet Island' => 'BV',
      'Brazil' => 'BR',
      'British Indian Ocean Territory' => 'IO',
      'British Virgin Islands' => 'VG',
      'Brunei' => 'BN',
      'Bulgaria' => 'BG',
      'Burkina Faso' => 'BF',
      'Burundi' => 'BI',
      'Cambodia' => 'KH',
      'Cameroon' => 'CM',
      'Canada' => 'CA',
      'Cape Verde' => 'CV',
      'Cayman Islands' => 'KY',
      'Central African Republic' => 'CF',
      'Chad' => 'TD',
      'Chile' => 'CL',
      'China' => 'CN',
      'Christmas Island' => 'CX',
      'Cocos [Keeling] Islands' => 'CC',
      'Colombia' => 'CO',
      'Comoros' => 'KM',
      'Congo - Brazzaville' => 'CG', 'Congo Rep.' => 'CG',
      'Congo - Kinshasa' => 'CD', 'Congo Dem. Rep.' => 'CD',
      'Cook Islands' => 'CK',
      'Costa Rica' => 'CR',
      'Croatia' => 'HR',
      'Cuba' => 'CU',
      'Cyprus' => 'CY',
      'Czech Republic' => 'CZ',
      'Côte d\'Ivoire' => 'CI',
      'Denmark' => 'DK',
      'Djibouti' => 'DJ',
      'Dominica' => 'DM',
      'Dominican Republic' => 'DO',
      'Ecuador' => 'EC',
      'Egypt' => 'EG',
      'El Salvador' => 'SV',
      'Equatorial Guinea' => 'GQ',
      'Eritrea' => 'ER',
      'Estonia' => 'EE',
      'Ethiopia' => 'ET',
      'Falkland Islands' => 'FK',
      'Faroe Islands' => 'FO',
      'Fiji' => 'FJ',
      'Finland' => 'FI',
      'France' => 'FR',
      'French Guiana' => 'GF',
      'French Polynesia' => 'PF',
      'French Southern Territories' => 'TF',
      'Gabon' => 'GA',
      'Gambia' => 'GM',
      'Georgia' => 'GE',
      'Germany' => 'DE',
      'Ghana' => 'GH',
      'Gibraltar' => 'GI',
      'Greece' => 'GR',
      'Greenland' => 'GL',
      'Grenada' => 'GD',
      'Guadeloupe' => 'GP',
      'Guam' => 'GU',
      'Guatemala' => 'GT',
      'Guernsey' => 'GG',
      'Guinea' => 'GN',
      'Guinea-Bissau' => 'GW',
      'Guyana' => 'GY',
      'Haiti' => 'HT',
      'Heard Island and McDonald Islands' => 'HM',
      'Honduras' => 'HN',
      'Hong Kong SAR China' => 'HK',
      'Hungary' => 'HU',
      'Iceland' => 'IS',
      'India' => 'IN',
      'Indonesia' => 'ID',
      'Iran' => 'IR',
      'Iraq' => 'IQ',
      'Ireland' => 'IE',
      'Isle of Man' => 'IM',
      'Israel' => 'IL',
      'Italy' => 'IT',
      'Jamaica' => 'JM',
      'Japan' => 'JP',
      'Jersey' => 'JE',
      'Jordan' => 'JO',
      'Kazakhstan' => 'KZ',
      'Kenya' => 'KE',
      'Kiribati' => 'KI',
      'Kuwait' => 'KW',
      'Kyrgyzstan' => 'KG', 'Kyrgyz Republic' => 'KG',
      'Laos' => 'LA', 'Lao PDR' => 'LA',
      'Latvia' => 'LV',
      'Lebanon' => 'LB',
      'Lesotho' => 'LS',
      'Liberia' => 'LR',
      'Libya' => 'LY',
      'Liechtenstein' => 'LI',
      'Lithuania' => 'LT',
      'Luxembourg' => 'LU',
      'Macau SAR China' => 'MO',
      'Macedonia' => 'MK',
      'Madagascar' => 'MG',
      'Malawi' => 'MW',
      'Malaysia' => 'MY',
      'Maldives' => 'MV',
      'Mali' => 'ML',
      'Malta' => 'MT',
      'Marshall Islands' => 'MH',
      'Martinique' => 'MQ',
      'Mauritania' => 'MR',
      'Mauritius' => 'MU',
      'Mayotte' => 'YT',
      'Mexico' => 'MX',
      'Micronesia' => 'FM', 'Micronesia Fed. Sts.' => 'FM',
      'Moldova' => 'MD',
      'Monaco' => 'MC',
      'Mongolia' => 'MN',
      'Montenegro' => 'ME',
      'Montserrat' => 'MS',
      'Morocco' => 'MA',
      'Mozambique' => 'MZ',
      'Myanmar [Burma]' => 'MM', 'Myanmar' => 'MM', 'Burma' => 'MM',
      'Namibia' => 'NA',
      'Nauru' => 'NR',
      'Nepal' => 'NP',
      'Netherlands' => 'NL',
      'Netherlands Antilles' => 'AN',
      'New Caledonia' => 'NC',
      'New Zealand' => 'NZ',
      'Nicaragua' => 'NI',
      'Niger' => 'NE',
      'Nigeria' => 'NG',
      'Niue' => 'NU',
      'Norfolk Island' => 'NF',
      'North Korea' => 'KP', 'Korea Dem. Rep.' => 'KP',
      'Northern Mariana Islands' => 'MP',
      'Norway' => 'NO',
      'Oman' => 'OM',
      'Pakistan' => 'PK',
      'Palau' => 'PW',
      'Palestinian Territories' => 'PS',
      'Panama' => 'PA',
      'Papua New Guinea' => 'PG',
      'Paraguay' => 'PY',
      'Peru' => 'PE',
      'Philippines' => 'PH',
      'Pitcairn Islands' => 'PN',
      'Poland' => 'PL',
      'Portugal' => 'PT',
      'Puerto Rico' => 'PR',
      'Qatar' => 'QA',
      'Romania' => 'RO',
      'Russia' => 'RU',
      'Rwanda' => 'RW',
      'Réunion' => 'RE',
      'Saint Barthélemy' => 'BL',
      'Saint Helena' => 'SH',
      'Saint Kitts and Nevis' => 'KN',
      'Saint Lucia' => 'LC',
      'Saint Martin' => 'MF',
      'Saint Pierre and Miquelon' => 'PM',
      'Saint Vincent and the Grenadines' => 'VC',
      'Samoa' => 'WS',
      'San Marino' => 'SM',
      'Saudi Arabia' => 'SA',
      'Senegal' => 'SN',
      'Serbia' => 'RS',
      'Seychelles' => 'SC',
      'Sierra Leone' => 'SL',
      'Singapore' => 'SG',
      'Slovakia' => 'SK',
      'Slovenia' => 'SI',
      'Solomon Islands' => 'SB',
      'Somalia' => 'SO',
      'South Africa' => 'ZA',
      'South Georgia and the South Sandwich Islands' => 'GS',
      'South Korea' => 'KR',
      'Spain' => 'ES',
      'Sri Lanka' => 'LK',
      'Sudan' => 'SD',
      'South Sudan' => 'SS',
      'Suriname' => 'SR',
      'Svalbard and Jan Mayen' => 'SJ',
      'Swaziland' => 'SZ',
      'Sweden' => 'SE',
      'Switzerland' => 'CH',
      'Syria' => 'SY', 'Syrian Arab Republic' => 'SY',
      'São Tomé and Príncipe' => 'ST', 'Sao Tome and Principe' => 'ST',
      'Taiwan' => 'TW',
      'Tajikistan' => 'TJ',
      'Tanzania' => 'TZ',
      'Thailand' => 'TH',
      'Timor-Leste' => 'TL',
      'Togo' => 'TG',
      'Tokelau' => 'TK',
      'Tonga' => 'TO',
      'Trinidad and Tobago' => 'TT',
      'Tunisia' => 'TN',
      'Turkey' => 'TR',
      'Turkmenistan' => 'TM',
      'Turks and Caicos Islands' => 'TC',
      'Tuvalu' => 'TV',
      'U.S. Minor Outlying Islands' => 'UM',
      'U.S. Virgin Islands' => 'VI',
      'Uganda' => 'UG',
      'Ukraine' => 'UA',
      'United Arab Emirates' => 'AE',
      'United Kingdom' => 'GB',
      'United States' => 'US',
      'Uruguay' => 'UY',
      'Uzbekistan' => 'UZ',
      'Vanuatu' => 'VU',
      'Vatican City' => 'VA',
      'Venezuela' => 'VE',
      'Vietnam' => 'VN',
      'Wallis and Futuna' => 'WF',
      'Western Sahara' => 'EH',
      'Yemen' => 'YE',
      'Zambia' => 'ZM',
      'Zimbabwe' => 'ZW',
      'Åland Islands' => 'AX');
  }

  public function frontendStatusLabel($order) {
    return $order->getStatusLabel();
  }

  public function getMailFromLang($lang) {
    switch ($lang) {
    case 'Chinese':
    case 'chinois':
      return "bej_comprod@icrc.org";
      //return "bej_comprod@data.fr";
    case 'Portuguese':
    case 'portuguais':
      return "bue_log@icrc.org";
      //return "bue_log@data.fr";
    case 'Russian':
    case 'russe':
      return "moscow@icrc.org";
      //return "mos_log@data.fr";
    case 'Spanish':
    case 'espagnol':
      return "bue_log@icrc.org";
      //return "bue_log@data.fr";
    case 'Arabic':
    case 'arabe':
      return "CAI_Logistics_Services@icrc.org";
      //return "cai_csc@data.fr";
    }
    return Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
  }
}


<?php

class Data_Icrc_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
  public function addItemFirst($type, $name, $params=null, $if=null, $cond=null)
  {
    if ($type === 'skin_css' && empty($params)) {
        $params = 'media="all"';
    }
    $this->_data['items_first'][$type.'/'.$name] = array(
        'type'   => $type,
        'name'   => $name,
        'params' => $params,
        'if'     => $if,
        'cond'   => $cond,
    );
    return $this;
  }

  public function getCssJsHtml()
  {
    if (!array_key_exists('items_first', $this->_data))
      return parent::getCssJsHtml();
    $tmp = $this->_data['items'];
    $this->_data['items'] = $this->_data['items_first'];
    $html = parent::getCssJsHtml();
    $this->_data['items'] = $tmp;
    $html .= parent::getCssJsHtml();
    return $html;
  }
}


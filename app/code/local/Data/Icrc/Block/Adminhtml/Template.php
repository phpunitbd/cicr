<?php

/**
 * useless if template files are present :(
 */
class Data_Icrc_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Template
{
  public function getTemplateFile()
  {
    $file = Mage::getBaseDir('design') . DS . 
              'adminhtml' . DS . 
              'default' . DS . 
              Mage::getStoreConfig('design/admin/theme') . DS . 
              $this->getTemplate();

    if (file_exists($file))
      return $file;
    else
      return parent::getTemplateFile();
  }

  public function _toHtml()
  {
    ob_start();

    $file = $this->getTemplateFile();
    if (!file_exists($file))
      $filename = Mage::getBaseDir('design') . DS .$file;
    else
      $filename = $file;
    if (file_exists($filename))
      include $filename;
    else
      echo "404 File not found ($filename)";

    return ob_get_clean();
  }
}

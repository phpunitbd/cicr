<?php

class Data_Icrc_Model_Api2_Productchanged extends Mage_Api2_Model_Resource
{    
    /**
     * get Renderer
     */
    public function getRenderer() {
        if (!$this->_renderer) {
            $renderer = Mage::getModel('data_icrc/api2_renderer_xml');
            $this->setRenderer($renderer);
        }
        return $this->_renderer;
    }

    /**
     * get Session
     */
    public function getSession() {
        return Mage::getSingleton('core/session');
    }
    
    /**
     * Render data using registered Renderer
     *
     * @param mixed $data
     */
    protected function _render($data)
    {
        $config = new Mage_Core_Model_Config();
        if(Mage::getStoreConfig('data_icrc/restcall') == 0) {
            $config ->saveConfig('data_icrc/restcall', 1);
            $this->getResponse()->setMimeType($this->getRenderer()->getMimeType())
            ->setBody($this->getRenderer()->render($data));
            $config ->saveConfig('data_icrc/restcall', 0);
        } else {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<error>Un webservice est déjà en cours d\'appel</error>';
            $this->getResponse()->setMimeType(Mage::getModel('api2/renderer_xml')->getMimeType())->setBody($xml);
        }
    }
}

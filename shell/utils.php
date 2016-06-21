<?php
require_once 'abstract.php';

class Mage_Shell_Utils extends Mage_Shell_Abstract
{
    protected function _construct()
    {
        parent::_construct();

        /**
         * Make sure to use adminhtml events
         */
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_Core_Model_App_Area::PART_EVENTS);
    }

    public function run()
    {
        if($this->getArg('majParent')) {
            echo "----- Sauvegarde produits commencement -----";
            Mage::getSingleton('core/session')->setUpdatedAt(0);
            
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection->addAttributeToSelect('*');
            
            foreach ($collection as $_product) {
                 echo "Product save : ".$_product->getSku();
                $_product->save();
            }
            
            Mage::getSingleton('core/session')->unsUpdatedAt();
            echo "----- Sauvegarde produits fin -----";
        }
    }
    
     /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
        --majParent
USAGE;
    }
}

$shell = new Mage_Shell_Utils();
$shell->run();
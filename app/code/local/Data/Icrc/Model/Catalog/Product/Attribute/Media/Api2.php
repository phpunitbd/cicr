<?php

class Data_Icrc_Model_Catalog_Product_Attribute_Media_Api2 extends Mage_Catalog_Model_Product_Attribute_Media_Api_V2
{
  protected function _test($step) {
    $query = "select * from `catalog_product_entity_int` where store_id = 1";
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $results = $readConnection->fetchAll($query);
    $count = count($results);
    Data_Icrc_Helper_Debug::msg("${step}: ${count}");
  }
  
  protected function _log($log = true) {
    $query = "SET global general_log = " . ($log ? '1' : '0');
    $h = mysql_connect('localhost', 'root', 'klodio');
    mysql_query($query, $h);
  }

  public function create($productId, $data, $store = null, $identifierType = null)
  {
    Data_Icrc_Helper_Debug::msg('catalogProductAttributeMediaCreate', false);

    $data = $this->_prepareImageData($data);

    $product = $this->_initProduct($productId, $store, $identifierType);
    //Data_Icrc_Helper_Debug::msgdump('init: ', $product);

    $gallery = $this->_getGalleryAttribute($product);

    if (!isset($data['file']) || !isset($data['file']['mime']) || !isset($data['file']['content'])) {
      $this->_fault('data_invalid', Mage::helper('catalog')->__('The image is not specified.'));
    }

    if (!isset($this->_mimeTypes[$data['file']['mime']])) {
      $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
    }

    $fileContent = @base64_decode($data['file']['content'], true);
    if (!$fileContent) {
      $this->_fault('data_invalid', Mage::helper('catalog')->__('The image contents is not valid base64 data.'));
    }

    unset($data['file']['content']);

    $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . $this->_getSession()->getSessionId();

    if (isset($data['file']['name']) && $data['file']['name']) {
      $fileName  = $data['file']['name'];
    } else {
      $fileName  = 'image';
    }
    $fileName .= '.' . $this->_mimeTypes[$data['file']['mime']];

    $ioAdapter = new Varien_Io_File();
    try {
      // Create temporary directory for api
      $ioAdapter->checkAndCreateFolder($tmpDirectory);
      $ioAdapter->open(array('path'=>$tmpDirectory));
      // Write image file
      $ioAdapter->write($fileName, $fileContent, 0666);
      unset($fileContent);

      // try to create Image object - it fails with Exception if image is not supported
      try {
        new Varien_Image($tmpDirectory . DS . $fileName);
      } catch (Exception $e) {
        // Remove temporary directory
        $ioAdapter->rmdir($tmpDirectory, true);

        throw new Mage_Core_Exception($e->getMessage());
      }

      // Adding image to gallery
      $file = $gallery->getBackend()->addImage(
        $product,
        $tmpDirectory . DS . $fileName,
        null,
        true
      );

      // Remove temporary directory
      $ioAdapter->rmdir($tmpDirectory, true);

      $gallery->getBackend()->updateImage($product, $file, $data);

      if (isset($data['types'])) {
        $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
      }

      $this->cleanSaveProduct($product);
    } catch (Mage_Core_Exception $e) {
      $this->_fault('not_created', $e->getMessage());
    } catch (Exception $e) {
      $this->_fault('not_created', Mage::helper('catalog')->__('Cannot create image.'));
    }
    return $gallery->getBackend()->getRenamedImage($file);
  }
  
  /**
  * This function removes unused EAV attributes to prevent them to be recorded
  * even if not modified (store/website attributes are written if not in default
  * store, even if not modified, see Mage_Catalog_Model_Resource_Abstract::_canUpdateAttribute)
  */
  protected function cleanSaveProduct($product) {
    Data_Icrc_Helper_Debug::msgdump('before clean: ', $product);
    $staticAttributes = array_keys(Mage::helper('data_icrc/product')->getStaticFields());
    $staticAttributes[] = 'store_id';
    $staticAttributes[] = 'visibility';
    $staticAttributes[] = 'url_key';
    //$staticAttributes[] = 'cost';
    //$staticAttributes[] = 'weight';
    foreach (Data_Icrc_Model_Catalog_Product_Attribute_ACCESSOR_Media::getDefaultValuedAttr($product) as $attr)
      $staticAttributes[] = $attr;
    Data_Icrc_Helper_Debug::msgdump('staticAttributes: ', $staticAttributes);
    $imageAttributes = array('image', 'small_image', 'thumbnail', 'media_gallery');
    foreach ($product->getData() as $k => $v) {
      if (in_array($k, $staticAttributes))
        continue;
      if (in_array($k, $imageAttributes))
        continue;
      Data_Icrc_Model_Catalog_Product_Open::removeAttribute($product, $k);
    }
    Data_Icrc_Helper_Debug::msgdump('after clean: ', $product);
    $product->save();
  }
}

class Data_Icrc_Model_Catalog_Product_Attribute_ACCESSOR_Media extends Mage_Catalog_Model_Abstract
{
  static function getDefaultValuedAttr($product) {
    return array_keys($product->_defaultValues);
  }
}


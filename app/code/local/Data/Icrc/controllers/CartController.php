<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart controller
 */
require_once 'Mage/Checkout/controllers/CartController.php';
class Data_Icrc_CartController extends Mage_Checkout_CartController
{
  protected function _error($msg) {
    $response = array();
    $response['status'] = 'ERROR';
    $response['error'] = $msg;
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
  }

  protected function _getCartHtml() {
  }

  /**
   * Add product to shopping cart action
   */
  public function addAction()
  {
    $params = $this->getRequest()->getParams();
    if (array_key_exists('gotoCart', $params) && $params['gotoCart'] == 1) {
      $this->_getSession()->setRedirectUrl(Mage::helper('checkout/cart')->getCartUrl());
    }
    if (array_key_exists('isAjax', $params) && $params['isAjax'] == 1) {
      return $this->addAjaxAction();
    }
    else {
      return parent::addAction();
    }
  }

  /**
   * Add product to shopping cart action
   */
  public function addAjaxAction()
  {
    $cart   = $this->_getCart();
    $params = $this->getRequest()->getParams();
    try {
      if (isset($params['qty'])) {
          $filter = new Zend_Filter_LocalizedToNormalized(
            array('locale' => Mage::app()->getLocale()->getLocaleCode())
          );
          if (is_array($params['qty'])) {
            foreach ($params['qty'] as &$q) {
              $q = $filter->filter($q);
            }
          }
          else {
            $params['qty'] = $filter->filter($params['qty']);
          }
      }
      if (isset($params['qty']) && is_array($params['qty'])) {
        $message = array();
        foreach ($params['qty'] as $id => $qty) {
          $product = $this->initGivenProduct($id);
          if (!$product->getId()) {
            $this->_error($this->__('Cannot add %s to shopping cart: not available', $product->getName()));
            return;
          }
          $cart->addProduct($product, array('qty' => $qty));
          $message[] = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
        }
      }
      else {
        $product = $this->_initProduct();
        /**
         * Check product availability
         */
        if (!$product) {
          $this->_error($this->__('Cannot add the item to shopping cart: not available'));
          return;
        }

        $cart->addProduct($product, $params);
        $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
      }

      $cart->save();

      $this->_getSession()->setCartWasUpdated(true);
      /**
       * @todo remove wishlist observer processAddToCart
       */
      Mage::dispatchEvent('checkout_cart_add_product_complete',
        array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
      );

      $response = array();
      $response['status'] = 'SUCCESS';
      $response['message'] = $message;
      $this->loadLayout();
      $header = $this->getLayout()->getBlock('cart_header')->toHtml();
      $response['header'] = $header;
      $response['items'] = Mage::helper('data_icrc')->getItemCount($cart);

      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    } catch (Mage_Core_Exception $e) {
      $this->_error(Mage::helper('core')->escapeHtml($e->getMessage()));
    } catch (Exception $e) {
      $msg = $this->__('Cannot add the item to shopping cart.');
      $this->_getSession()->addException($e, $msg);
      Mage::logException($e);
      $this->_error($msg . $e->getMessage());
    }
  }

  public function initGivenProduct($productId) {
    if ($productId) {
      $product = Mage::getModel('catalog/product')
        ->setStoreId(Mage::app()->getStore()->getId())
        ->load($productId);
      return $product;
    }
    return false;
  }
}

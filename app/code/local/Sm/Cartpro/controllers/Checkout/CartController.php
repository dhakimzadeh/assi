<?php
/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
require_once 'Mage/Checkout/controllers/CartController.php';   
class Sm_Cartpro_Checkout_CartController extends Mage_Checkout_CartController 
{
	protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
	
	public function addAction()
    {
		if (!$this->getRequest()->isAjax())
			return parent::addAction();
		$result = array();
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		
        if (isset($params['form_key']) && !$this->_validateFormKey())
			 Mage::throwException('Invalid form key');
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
			 
            $product = $this->_initProduct();
			$product_url = $product->getProductUrl();
			$stock = $product->getStockItem();
			if (isset($params['qty']))  {
				$qty = (int)$params['qty'];
				if($stock->getMaxSaleQty() && $qty > $stock->getMaxSaleQty()) {
					Mage::throwException($this->__('The maximum quantity allowed for purchase is %s.', $stock->getMaxSaleQty() * 1));
				}else if($qty <= 0){
					 Mage::throwException('Qty is not valid.');
				}
			}
			
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $result['error'] = $this->__('Product '.(int)$params['product'].' is not valid');
            }
			
            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
					$this->loadLayout();
					$result['qty'] = $this->_getCart()->getSummaryQty();
                    $result['success'] = 1;
					$result['message'] = $this->__('<strong>%s</strong> was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
					$result['content'] = $this->getLayout()->getBlock('mini_cartpro_content')->toHtml();
					$result['top_link'] = $this->getLayout()->getBlock('top.links')->toHtml();
					$checkout_content = $this->getLayout()->getBlock('checkout.cart');
					if($checkout_content)
						$result['checkout_content'] = $checkout_content->toHtml();
					else
						$result['checkout_content'] = '';
					$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
					if($sidebar_block) {
						$sidebar = $sidebar_block->toHtml();
						$result['block_wishlist'] = $sidebar;
					} else{
						$result['block_wishlist'] = '';
					}

					$result['nb_items'] = Mage::helper('wishlist')->getItemCount();
                }
				else{
					$result['success'] = 0;
					$_err = '';
					$errors = $cart->getQuote()->getErrors();
					if(!empty($errors) && is_array($errors)){
						foreach($errors as $i => $err){ 
							 $_err .= $err->getCode().(($i > 1 && $i < count($errors) - 1) ?'<br/>':'');
						}
					}
					$result['error'] = $_err;
				}
            }
		} catch (Mage_Core_Exception $e) {
			
		   if(!empty($product)){
			    $result['success'] = 0;
				$result['url'] = $product_url;
				$result['error'] = $this->__($e->getMessage());
				$this->getResponse()->setHeader('Content-type', 'application/json');
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			}else{
				$result['success'] = 0;
				$result['error'] = $this->__($e->getMessage());
			}
			
	    } catch (Exception $e) {
			$result['success'] = 0;
            $result['error'] = $this->__($e->getMessage());
        }
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	/**
     * Minicart delete action
     */
    public function ajaxDeleteAction()
    {
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $result = array();
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();

                $result['qty'] = $this->_getCart()->getSummaryQty();

                $this->loadLayout();
                $result['content'] = $this->getLayout()->getBlock('mini_cartpro_content')->toHtml();
                $result['success'] = 1;
                $result['message'] = $this->__('Item was removed successfully.');
				$result['checkout_content'] = $this->getLayout()->getBlock('checkout.cart')->toHtml();
				$result['top_link'] = $this->getLayout()->getBlock('top.links')->toHtml();
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__($e->getMessage());
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	/**
     * Minicart ajax update qty action
     */
    public function ajaxUpdateAction()
    {
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $id = (int)$this->getRequest()->getParam('id');
        $qty = (int)$this->getRequest()->getParam('qty');
        $result = array();
        if ($id) {
            try {
                $cart = $this->_getCart();
                if (isset($qty)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $qty = $filter->filter($qty);
                }

                $quoteItem = $cart->getQuote()->getItemById($id);
                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }
				$stock = $quoteItem->getProduct()->getStockItem();
				if ($qty == 0)
                    $cart->removeItem($id);
				
				if($stock->getMaxSaleQty() && $qty > $stock->getMaxSaleQty()) {
					Mage::throwException($this->__('The maximum quantity allowed for purchase is %s.', $stock->getMaxSaleQty() * 1));
				}else if($qty <= 0){
					 Mage::throwException('Qty is not valid.');
				}
				
				$quoteItem->setQty($qty)->save();
                if (!$quoteItem->getHasError()) {
					$this->_getCart()->save();
					$this->loadLayout();
					$result['success'] = 1;
                    $result['message'] = $this->__('Item was updated successfully.');
					$result['content'] = $this->getLayout()->getBlock('mini_cartpro_content')->toHtml();
					$result['qty'] = $this->_getCart()->getSummaryQty();
					$result['checkout_content'] = $this->getLayout()->getBlock('checkout.cart')->toHtml();
					$result['top_link'] = $this->getLayout()->getBlock('top.links')->toHtml();
                } else {
					$result['success'] = 0;
                    $result['error'] = $quoteItem->getMessage();
                }
               
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__($e->getMessage());
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	 /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction()
    {
        if (!$this->getRequest()->isAjax())
			return parent::addAction();
		$cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();
        if (!isset($params['options'])) 
            $params['options'] = array();
        
		$result = array();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }
			
			if (isset($params['qty']))  {
				$stock = $quoteItem->getProduct()->getStockItem();
				$qty = (int)$params['qty'];
				if($stock->getMaxSaleQty() && $qty > $stock->getMaxSaleQty()) {
					Mage::throwException($this->__('The maximum quantity allowed for purchase is %s.', $stock->getMaxSaleQty() * 1));
				}else if($qty <= 0){
					 Mage::throwException('Qty is not valid.');
				}
			}

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
			
			if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
					$this->loadLayout();
					$result['qty'] = $this->_getCart()->getSummaryQty();
                    $result['success'] = 1;
					$result['message'] = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
					$result['content'] = $this->getLayout()->getBlock('mini_cartpro_content')->toHtml();
                }
				else{
					$result['success'] = 0;
					$_err = '';
					$errors = $cart->getQuote()->getErrors();
					if(!empty($errors) && is_array($errors)){
						foreach($errors as $i => $err){ 
							 $_err .= $err->getCode().(($i > 1 && $i < count($errors) - 1) ?'<br/>':'');
						}
					}
					$result['error'] = $_err;
				}
            }
			
        } catch (Mage_Core_Exception $e) {
			$result['success'] = 0;
			$result['error'] = $this->__($e->getMessage());
			
	    } catch (Exception $e) {
			$result['success'] = 0;
			$result['error'] = $this->__('Cannot update the item.');
        }
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
}
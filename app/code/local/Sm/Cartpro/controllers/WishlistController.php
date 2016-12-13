<?php
/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

require_once 'Mage/Wishlist/controllers/IndexController.php';
class Sm_Cartpro_WishlistController extends Mage_Wishlist_IndexController{
	/**
     * Retrieve wishlist object
     * @param int $wishlistId
     * @return Mage_Wishlist_Model_Wishlist|bool
     */
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) 
            return $wishlist;
        try {
            if (!$wishlistId) 
                $wishlistId = $this->getRequest()->getParam('wishlist_id');

            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }

        return $wishlist;
    }
	
	 /**
     * Add the item to wish list
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
	public function addAction()
    {
		if (!$this->getRequest()->isAjax())
			return parent::addAction();
		$result = array();
		
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
		
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $result['success'] = 0;
            $result['error'] = $this->__('Wishlist Has Been Disabled By Admin');
        }
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $result['success'] = 0;
            $result['error'] = $this->__('Please Login First');
        }

        if(empty($result)){
			$result['is_wishlist'] = 1;
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $result['success'] = 0;
                $result['error'] = $this->__('Unable to Create Wishlist');
            }else{
 
                $productId = (int) $this->getRequest()->getParam('product');
                if (!$productId) {
                    $result['success'] = 0;
                    $result['error'] = $this->__('Item not found.');
                }else{
 
                    $product = Mage::getModel('catalog/product')->load($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $result['success'] = 0;
                        $result['error'] = $this->__('Cannot specify product.');
                    }else{
 
                        try {
                            $requestParams = $this->getRequest()->getParams();
                            $buyRequest = new Varien_Object($requestParams);
 
                            $_result = $wishlist->addNewItem($product, $buyRequest);
                            if (is_string($result)) {
                                Mage::throwException($_result);
                            }
                            $wishlist->save();
 
                            Mage::dispatchEvent(
                                'wishlist_add_product',
                            array(
                                'wishlist'  => $wishlist,
                                'product'   => $product,
                                'item'      => $_result
                            )
                            );
							 $referer = $session->getBeforeWishlistUrl();
							if ($referer) {
								$session->setBeforeWishlistUrl(null);
							} else {
								$referer = $this->_getRefererUrl();
							}
											
                            /**
							 *  Set referer to avoid referring to the compare popup window
							 */
							$session->setAddActionReferer($referer);

							Mage::helper('wishlist')->calculate();
 
                            $message = $this->__('<strong>%1$s</strong> has been added to your wishlist.', $product->getName(),  Mage::helper('core')->escapeUrl($referer));
                            $result['success'] = 1;
                            $result['message'] = $message;
 
                            Mage::unregister('wishlist');
 
                            $this->loadLayout();
                            $toplink = $this->getLayout()->getBlock('top.links')->toHtml();

							$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
							if($sidebar_block) {
								$sidebar = $sidebar_block->toHtml();
								$result['block_wishlist'] = $sidebar;
							} else{
								$result['block_wishlist'] = '';
							}

							$result['top_link'] = $toplink;
							$result['nb_items'] = Mage::helper('wishlist')->getItemCount();
                        }
                        catch (Mage_Core_Exception $e) {
                            $result['success'] = 0;
                            $result['error'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
                        }
                        catch (Exception $e) {
                            mage::log($e->getMessage());
                            $result['success'] = '0';
                            $result['error'] = $this->__($e->getMessage());
                        }
                    }
                }
            }
 
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	 /**
     * Remove item
     */
    public function removeAction()
    {
        if (!$this->getRequest()->isAjax())
			return parent::removeAction();
		
		if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $result['success'] = 0;
            $result['error'] = $this->__('Wishlist Has Been Disabled By Admin');
        }
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $result['success'] = 0;
            $result['error'] = $this->__('Please Login First');
        }
		$result = array();
		
		$id = (int) $this->getRequest()->getParam('item');
        try {
			$result['is_wishlist'] = 1;
			$item = Mage::getModel('wishlist/item')->load($id);
			if (!$item->getId()) 
				Mage::throwException('Item not found.');
			
			$wishlist = $this->_getWishlist($item->getWishlistId());
			if (!$wishlist) {
				$result['success'] = 0;
				$result['error'] = $this->__('An error occurred while deleting the item from wishlist.');
			}else{
				$item->delete();
				$wishlist->save();
				Mage::helper('wishlist')->calculate();
				$result['success'] = 1;
				$result['message'] = $this->__('Item has been removed from your wishlist.');
				$this->loadLayout();
				$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
				$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
				if($sidebar_block){
					$result['block_wishlist'] = $sidebar_block->toHtml();
				}else
					$result['block_wishlist'] = '';
				$result['top_link'] = $toplink;
				$result['nb_items'] = Mage::helper('wishlist')->getItemCount();
				$customer_wishlist = $this->getLayout()->getBlock('customer.wishlist');
				$result['customer_wishlist'] = $customer_wishlist->toHtml();
			}
			
            
        } catch (Mage_Core_Exception $e) {
			$result['success'] = 0;
            $result['error'] =  $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage());
        } catch (Exception $e) {
            $result['success'] = 0;
            $result['error'] = $this->__($e->getMessage());
        }
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	/**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     */
    public function cartAction()
    {
		if (!$this->getRequest()->isAjax())
			return parent::removeAction();
        $result = array();
		
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
		
		$itemId = (int) $this->getRequest()->getParam('item');
		/* @var $item Mage_Wishlist_Model_Item */
		$item = Mage::getModel('wishlist/item')->load($itemId);
		$item_url = $item->getProduct()->getProductUrl();
        try {
			if (!$item->getId()) {
				$result['success'] = 0;
				$result['error'] = $this->__('Item not Found');
			}
			$wishlist = $this->_getWishlist($item->getWishlistId());
			
			if (!$wishlist) {
				$result['success'] = 0;
				$result['error'] = $this->__('Cannot add item to shopping cart');
			}
			// Set qty
			$qty = (int)$this->getRequest()->getParam('qty');
			if (is_array($qty)) {
				if (isset($qty[$itemId])) {
					$qty = $qty[$itemId];
				} else {
					$qty = 1;
				}
				
				$stock = $item->getProduct()->getStockItem();
				if ($qty <= 0){
					Mage::throwException('Qty is not valid.');
				}else if($stock->getMaxSaleQty() && $qty > $stock->getMaxSaleQty()) {
					Mage::throwException($this->__('The maximum quantity allowed for purchase is %s.', $stock->getMaxSaleQty() * 1));
				}
			}
			$qty = $this->_processLocalizedQty($qty);

			if ($qty) {
				$item->setQty($qty);
			}

			/* @var $session Mage_Wishlist_Model_Session */
			$session    = Mage::getSingleton('wishlist/session');
			$cart       = Mage::getSingleton('checkout/cart');
            $options = Mage::getModel('wishlist/item_option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                array('current_config' => $item->getBuyRequest())
            );

            $item->mergeBuyRequest($buyRequest);
            if ($item->addToCart($cart, true)) {
                $cart->save()->getQuote()->collectTotals();
            }

            $wishlist->save();
            Mage::helper('wishlist')->calculate();
            
			$result['is_wishlist'] = 1;
			if (!$cart->getQuote()->getHasError()) {
				$this->loadLayout();
				$result['qty'] =  Mage::getSingleton('checkout/cart')->getSummaryQty();
				$result['success'] = 1;
				$result['message'] = $this->__('<strong>%s</strong> was added to your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
				$result['content'] = $this->getLayout()->getBlock('mini_cartpro_content')->toHtml();
				$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
				$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
				if($sidebar_block) {
					$sidebar = $sidebar_block->toHtml();
					$result['block_wishlist'] = $sidebar;
				} else{
					$result['block_wishlist'] = '';
				}
				$result['top_link'] = $toplink;
				$result['nb_items'] = Mage::helper('wishlist')->getItemCount();
				$customer_wishlist = $this->getLayout()->getBlock('customer.wishlist');
				$result['customer_wishlist'] = $customer_wishlist->toHtml();
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
			
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
				$result['success'] = 0;
				$result['error'] = $this->__('Cannot add item to shopping cart');
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
				$result['success'] = 0;
				$result['error'] = $this->__($e->getMessage());
            } else {
				$result['success'] = 0;
				$result['error'] = $messages[] = $this->__('%s for <a href="%s" class="cp-close" title="%s">"%s"</a>', trim($e->getMessage(), '.'), Mage::helper('cartpro')->_getItemConfigureUrl($item), $item->getProduct()->getName(), $item->getProduct()->getName());
            }
			
        } catch (Exception $e) {
			$result['success'] =0;
			$result['error'] = $this->__('Cannot add item to shopping cart');
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	 /**
     * Action to accept new configuration for a wishlist item
     */
    public function updateItemOptionsAction()
    {
        if (!$this->getRequest()->isAjax())
			return parent::updateItemOptionsAction();
        $result = array();
		$result['is_wishlist'] = 1;
		$session = Mage::getSingleton('customer/session');
        try {
			
			$productId = (int) $this->getRequest()->getParam('product');
			if (!$productId) {
				$result['success'] = 0;
                Mage::throwException('Item not found.');
			}

			$product = Mage::getModel('catalog/product')->load($productId);
			if (!$product->getId() || !$product->isVisibleInCatalog()) {
				$result['success'] = 0;
                $result['error'] =  Mage::throwException('Cannot specify product.');
			}
			
            $id = (int) $this->getRequest()->getParam('id');
            /* @var Mage_Wishlist_Model_Item */
            $item = Mage::getModel('wishlist/item');
            $item->load($id);
            $wishlist = $this->_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                $result['success'] = 0;
				$result['error'] = $this->__('An error occurred while updating wishlist.');
            }else {
				$buyRequest = new Varien_Object($this->getRequest()->getParams());
				$wishlist->updateItem($id, $buyRequest)->save();

				Mage::helper('wishlist')->calculate();
				Mage::dispatchEvent('wishlist_update_item', array(
					'wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id))
				);

				Mage::helper('wishlist')->calculate();
				$result['success'] = 1;
				$message = $this->__('<strong>%1$s</strong> has been updated in your wishlist.', $product->getName());
				$result['message'] = $message ;
			}
        } catch (Mage_Core_Exception $e) {
			$result['success'] =0;
			$result['error'] = $this->__($e->getMessage());
        } catch (Exception $e) {
            $result['success'] =0;
			$result['error'] = $this->__('An error occurred while updating wishlist.');
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

	
}
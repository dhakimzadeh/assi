<?php
/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

require_once 'Mage/Catalog/controllers/Product/CompareController.php'; 

class Sm_Cartpro_Catalog_Product_CompareController extends Mage_Catalog_Product_CompareController{
	 /**
     * Add item to compare list
     */
    public function addAction()
    {
		if (!$this->getRequest()->isAjax())
			return parent::addAction();
		$result = array();
		$result['is_compare'] = 1;
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }

        $productId = (int) $this->getRequest()->getParam('product');
        try{
			if ($productId && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())) {
				$product = Mage::getModel('catalog/product')
					->setStoreId(Mage::app()->getStore()->getId())
					->load($productId);

				if ($product->getId()/* && !$product->isSuper()*/) {
					Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
					Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
				}
				Mage::helper('catalog/product_compare')->calculate();
				$this->loadLayout();
				$result['success'] = 1;
				$result['nb_items'] = Mage::helper('catalog/product_compare')->getItemCount();
				$result['message'] = $this->__('The product <strong>%s</strong> has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
				$result['block_compare'] = $this->getLayout()->getBlock('cartpro_compare_sidebar')->toHtml();
			}else{
				$result['success'] = 0;
				$result['error'] = $this->__('Can not add item to comparison list.');
					
			}
				
		}catch(Exception $e){
			$result['success'] = 0;
			$result['error'] = $this->__($e->getMessage());
			
		}
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	/**
     * Remove item from compare list
     */
    public function removeAction()
    {
		if (!$this->getRequest()->isAjax())
			return parent::removeAction();
		$result = array();
		$result['is_compare'] = 1;
		try{
			if ($productId = (int) $this->getRequest()->getParam('product')) {
				$product = Mage::getModel('catalog/product')
					->setStoreId(Mage::app()->getStore()->getId())
					->load($productId);

				if($product->getId()) {
					/** @var $item Mage_Catalog_Model_Product_Compare_Item */
					$item = Mage::getModel('catalog/product_compare_item');
					if(Mage::getSingleton('customer/session')->isLoggedIn()) {
						$item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
					} elseif ($this->_customerId) {
						$item->addCustomerData(
							Mage::getModel('customer/customer')->load($this->_customerId)
						);
					} else {
						$item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
					}

					$item->loadByProduct($product);

					if($item->getId()) {
						$item->delete();
						Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
						Mage::helper('catalog/product_compare')->calculate();
						
						$this->loadLayout();
						$result['success'] = 1;
						$result['nb_items'] = Mage::helper('catalog/product_compare')->getItemCount();
						$result['message'] = $this->__('The product <strong>%s</strong> has been removed from comparison list.', $product->getName());
						$result['block_compare'] = $this->getLayout()->getBlock('cartpro_compare_sidebar')->toHtml();
					}else{
						$result['success'] = 0;
						$result['error'] = $this->__('Can not remove item from comparison list.');
					}
				}
			}else{
				$result['success'] = 0;
				$result['error'] = $this->__('Can not remove item from comparison list.');
			}
			
		}catch(Exception $e){
			$result['success'] = 0;
			$result['error'] = $this->__($e->getMessage());
		}
		$this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	/**
     * Remove all items from comparison list
     */
    public function clearAction()
    {
		if (!$this->getRequest()->isAjax())
			return parent::removeAction();
		$result = array();
		$result['is_compare'] = 1;
		try{
			 $items = Mage::getResourceModel('catalog/product_compare_item_collection');
			
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				$items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
			} elseif ($this->_customerId) {
				$items->setCustomerId($this->_customerId);
			} else {
				$items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
			}

			/** @var $session Mage_Catalog_Model_Session */
			$session = Mage::getSingleton('catalog/session');

			try {
				$items->clear();
				Mage::helper('catalog/product_compare')->calculate();
				$this->loadLayout();
				$result['success'] = 1;
				$result['nb_items'] = Mage::helper('catalog/product_compare')->getItemCount();
				$result['message'] = $this->__('The comparison list was cleared.');
				$result['block_compare'] = $this->getLayout()->getBlock('cartpro_compare_sidebar')->toHtml();
			} catch (Mage_Core_Exception $e) {
				$result['success'] = 0;
				$result['error'] = $this->__($e->getMessage());
			} catch (Exception $e) {
				$session->addException($e, $this->__('An error occurred while clearing comparison list.'));
				$result['success'] = 0;
				$result['error'] = $this->__($e->getMessage());
			}
		}catch(Exception $e){
			$result['success'] = 0;
			$result['error'] = $this->__($e->getMessage());
		}
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
}
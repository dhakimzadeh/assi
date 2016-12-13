<?php
/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Cartpro_IndexController extends Mage_Core_Controller_Front_Action
{
	public function productAction(){
		$_path = strstr(Mage::app()->getRequest()->getRequestUri(),'/path');
		$_path = str_replace("/path/",'',$_path );
		
		$tableName = Mage::getSingleton('core/resource')->getTableName('core_url_rewrite');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$query = "select MAIN_TABLE.`product_id` from `{$tableName}` as MAIN_TABLE where MAIN_TABLE.`request_path` in('{$_path}')";
		$readresult = $write->query($query);
		
		if ($row = $readresult->fetch() ) {
			$productId=$row['product_id'];
		}
		$viewHelper = Mage::helper('catalog/product_view');
		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}	
}
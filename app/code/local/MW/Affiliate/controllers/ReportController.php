<?php

/**
 * @author Tuanlv
 * @copyright 2014
 */

class MW_Affiliate_ReportController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
  		$store_id = Mage::app()->getStore()->getId();
		if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate')->getAffiliateActive();
		$lock = Mage::helper('affiliate')->getAffiliateLock();
		if($active > 0)
		{
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('checkout/session');
			$this->renderLayout();
		}
		else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		} 
    } 
    
    public function dashboardAction(){
        if($this->getRequest()->getPost('ajax') == 'true'){
            $data = $this->getRequest()->getPost();
            $data['customer_id'] = Mage::getSingleton('customer/session')->getCustomer()->getId();

            switch($this->getRequest()->getPost('type'))
            {
                case 'dashboard':
                    print Mage::getModel('affiliate/report')->prepareCollectionFrontend($data);
                break;
            }

            exit;
        }
    }  
}

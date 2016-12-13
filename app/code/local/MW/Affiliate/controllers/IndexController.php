<?php
class MW_Affiliate_IndexController extends Mage_Core_Controller_Front_Action
{   
    public function preDispatch()
    {
        parent::preDispatch();
    	if (!$this->getRequest()->isDispatched()) {
            return;
        }
    	if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
    }
    public function testAction()
    {
    	Mage::getModel('affiliate/observer')->runCronHoldingCommission();
    	echo 'ngon';
    }
    public function indexAction()
    {  
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
    	$active = Mage::helper('affiliate') ->getAffiliateActive();
    	if($active == 0)
    	{
    		$this->_redirect('affiliate/index/createaccount/');
    	}
    	else if($active > 0){
    		$this->_redirect('affiliate/index/referralaccount/');
    	}
    }
	protected function _getSession()
    {
       return Mage::getSingleton('core/session');
    }
    
	public function referralaccountAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate') ->getAffiliateActive();
    	if($active == 0)
    	{
    		$this->_redirect('affiliate/index/createaccount/');
    	}
    	else if($active > 0 )
    	{
			$this->loadLayout(); 
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('core/session');    
			$this->renderLayout();	
    	}		
    }
    
	public function createaccountAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate') ->getAffiliateActive();
    	if($active == 0)
    	{
			$this->loadLayout(); 
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('core/session');    
			$this->renderLayout();
    	}
    	else if($active > 0) {
    		$this->_redirect('affiliate/index/referralaccount/');
    	}			
    		
    }
    
	public function listprogramAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate') ->getAffiliateActive();
		$lock = Mage::helper('affiliate') ->getAffiliateLock();
		if($active > 0)
		{   
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
			$this->loadLayout(); 
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('core/session');    
			$this->renderLayout();
		}
		else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		}
    }
    
	public function viewprogramAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate') ->getAffiliateActive();
		$lock = Mage::helper('affiliate') ->getAffiliateLock();
		if($active > 0)
		{   
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
			$this->loadLayout(); 
			$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
	    	if ($navigationBlock) {
	            $navigationBlock->setActive('affiliate/index/listprogram');
	        }
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('core/session');    
			$this->renderLayout();
		}
    	else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		}
    }
    
	public function viewhistoryAction()
    {
    	
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
    	$active = Mage::helper('affiliate') ->getAffiliateActive();
    	$lock = Mage::helper('affiliate') ->getAffiliateLock();
		if($active > 0)
		{   
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
			$this->loadLayout(); 
			$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
	    	if ($navigationBlock) {
	            $navigationBlock->setActive('affiliate/index/transaction');
	        }
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('core/session');    
			$this->renderLayout();
		}
    	else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		}			
    		
    }
    
	public function referralAction()
    {   
    	$store_id = Mage::app()->getStore()->getId();
    	$max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
	  	$min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
    	$customer_id = (int)Mage::getSingleton('customer/session')->getCustomer()->getId();
    	$getway_withdrawn = $this->getRequest()->getPost('getway_withdrawn');
    	$payment_email = $this->getRequest()->getPost('paypal_email');
    	
    	if($getway_withdrawn != 'banktransfer' && $getway_withdrawn != 'check')
    	{
	    	$collectionFilters = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
	                    			->addFieldToFilter('payment_email',$payment_email);

	        if(sizeof($collectionFilters) > 0)
	        { 
	         	foreach ($collectionFilters as $collectionFilter) 
	         	{
		        	if($collectionFilter->getCustomerId()!= $customer_id)
		           	{	
			        	$this->_getSession()->addError($this->__('There is already an account with this emails paypal'));
			           	$this->_redirect('affiliate/index/referralaccount/');
			           	return;
		           	}
	         	} 
	 		}
    	}
    	$auto_withdrawn = (int)$this->getRequest()->getPost('auto_withdrawn');
    	$payment_release_level = (double)$this->getRequest()->getPost('payment_release_level'); 
      	if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
        {
            if($payment_release_level < $min  || $payment_release_level > $max)
            {   
            	$this->_getSession()->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
            	$this->_redirect('affiliate/index/referralaccount/');
	           	return;
            }
        }
    	$reserve_level = $this->getRequest()->getPost('reserve_level');
    	if(!$reserve_level) {
    		$reserve_level = 0;
    	}
    	
    	$bank_name = '';
    	$name_account = '';
    	$bank_country = '';
    	$swift_bic = '';
    	$account_number = '';
    	$re_account_number = '';
    	$referral_site = '';
    	$referral_site = $this->getRequest()->getPost('referral_site');
    	
    	if($getway_withdrawn == 'check') {
    		$payment_email = '';
    	}
    	if($getway_withdrawn == 'banktransfer') {
    		$payment_email = '';
    		$bank_name = $this->getRequest()->getPost('bank_name');
	        $name_account = $this->getRequest()->getPost('name_account');
	        $bank_country = $this->getRequest()->getPost('bank_country');
	        $swift_bic = $this->getRequest()->getPost('swift_bic');
	        $account_number = $this->getRequest()->getPost('account_number');
	        $re_account_number = $this->getRequest()->getPost('re_account_number');
    	}
    	if(!$referral_site) {
    		$referral_site = '';
    	}
    	if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::MANUAL) {
    		$payment_release_level = 0;
    	}
   
    	$referral_edit = Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id);
    	$referral_edit->setPaymentGateway($getway_withdrawn);
    	$referral_edit->setPaymentEmail($payment_email);
    	$referral_edit->setAutoWithdrawn($auto_withdrawn);
    	$referral_edit->setWithdrawnLevel($payment_release_level);
    	$referral_edit->setReserveLevel($reserve_level);
    	$referral_edit->setBankName($bank_name);
		$referral_edit->setNameAccount($name_account);
		$referral_edit->setBankCountry($bank_country);
		$referral_edit->setSwiftBic($swift_bic);
		$referral_edit->setAccountNumber($account_number);
		$referral_edit->setReAccountNumber($re_account_number);
		$referral_edit->setReferralSite($referral_site);
    	$referral_edit->save();
    	
    	$this->_getSession()->addSuccess($this->__("You have successfully updated affiliate account"));
    	$this->_redirect('affiliate/index/referralaccount/');		
    }
    
	public function createpostAction()
    {   
    	$store_id = Mage::app()->getStore()->getId();
    	
    	$session = Mage::getSingleton('core/session');
    	$session->unsetData('check_affiliate');
        $session->unsetData('payment_gateway');
        $session->unsetData('payment_email');
        $session->unsetData('auto_withdrawn');
        $session->unsetData('withdrawn_level');
        $session->unsetData('reserve_level');
        $session->unsetData('bank_name');
        $session->unsetData('name_account');
        $session->unsetData('bank_country');
        $session->unsetData('swift_bic');
        $session->unsetData('referral_site');
        
    	$max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
	  	$min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
    	$payment_email = $this->getRequest()->getPost('paypal_email');
    	$getway_withdrawn = $this->getRequest()->getPost('getway_withdrawn');
    	$check_affiliate = $this->getRequest()->getPost('check_affiliate');
    	$reserve_level = $this->getRequest()->getPost('reserve_level');
    	$auto_withdrawn = (int)$this->getRequest()->getPost('auto_withdrawn');
    	$payment_release_level = (double)$this->getRequest()->getPost('payment_release_level');
    	$bank_name = '';
    	$name_account = '';
    	$bank_country = '';
    	$swift_bic = '';
    	$account_number = '';
    	$re_account_number = '';
    	$referral_site = '';
    	$referral_site = $this->getRequest()->getPost('referral_site');
    	
    	if($getway_withdrawn == 'check') 
    	{
    		$payment_email = '';
    	}
    	if($getway_withdrawn == 'banktransfer') 
    	{
    		$payment_email = '';
    		$bank_name = $this->getRequest()->getPost('bank_name');
	        $name_account = $this->getRequest()->getPost('name_account');
	        $bank_country = $this->getRequest()->getPost('bank_country');
	        $swift_bic = $this->getRequest()->getPost('swift_bic');
	        $account_number = $this->getRequest()->getPost('account_number');
	        $re_account_number = $this->getRequest()->getPost('re_account_number');
    	}
    	if($check_affiliate) 
    	{
    		// set session
			$session->setCheckAffiliate($check_affiliate);
			$session->setPaymentGateway($getway_withdrawn);
			$session->setPaymentEmail($payment_email);
			$session->setAutoWithdrawn($auto_withdrawn);
			$session->setBankName($bank_name);
			$session->setNameAccount($name_account);
			$session->setBankCountry($bank_country);
			$session->setSwiftBic($swift_bic);
			
			if($referral_site){
				$session->setReferralSite($referral_site);
			}
			if($payment_release_level) {
				$session->setWithdrawnLevel($payment_release_level);
			}
			if($reserve_level) {
				$session->setReserveLevel($reserve_level);
			}
			if($getway_withdrawn != 'banktransfer' && $getway_withdrawn != 'check') 
			{
	    		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
	                    			->addFieldToFilter('payment_email',$payment_email);
	            if(sizeof($collectionFilter) > 0)
	            {
	            	$this->_getSession()->addError($this->__('There is already an account with this emails paypal'));
	            	$this->_redirect('affiliate/index/createaccount/');
	            	return;
	            }
			}
	    	if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
	        {
		    	if($payment_release_level < $min  || $payment_release_level > $max)
		        {
		        	$this->_getSession()->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
		            $this->_redirect('affiliate/index/createaccount/');
		           	return;
	            }
           	}
           	
	    	if(!$reserve_level) {
	    		$reserve_level = 0;
	    	}
	    	if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::MANUAL)
	    	{
	    		$payment_release_level = 0;
	    	}
	    	
	    	$customer_id = (int)Mage::getSingleton('customer/session')->getCustomer()->getId();
	    	$active = MW_Affiliate_Model_Statusactive::PENDING;
	    	
	    	// neu cau hinh config tu approved
	    	$auto_approved = Mage::helper('affiliate/data')->getAutoApproveRegisterStore($store_id);
		    if($auto_approved) {
		    	$active = MW_Affiliate_Model_Statusactive::ACTIVE;
		    }
	    	$customers = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    				      ->addFieldToFilter('customer_id',$customer_id);
            if(sizeof($customers) > 0)
            {
            	$referral_edit = Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id);
		    	$referral_edit->setActive($active);
		    	$referral_edit->setPaymentGateway($getway_withdrawn);
		    	$referral_edit->setPaymentEmail($payment_email);
		    	$referral_edit->setAutoWithdrawn($auto_withdrawn);
		    	$referral_edit->setWithdrawnLevel($payment_release_level);
		    	$referral_edit->setReserveLevel($reserve_level);
		    	$referral_edit->setBankName($bank_name);
				$referral_edit->setNameAccount($name_account);
				$referral_edit->setBankCountry($bank_country);
				$referral_edit->setSwiftBic($swift_bic);
				$referral_edit->setAccountNumber($account_number);
				$referral_edit->setReAccountNumber($re_account_number);
				//$referral_edit->setReferralSite($referral_site);
		    	$referral_edit->save();
            }
            // trong truong hop dk la thanh vien website nhung ko luu vao bang affiliatecustomer
            else if(sizeof($customers) == 0)
            {
		        $cokie = (int)Mage::getModel('core/cookie')->get('customer');

		        // neu khong ton tai cokie cua thanh vien gioi thieu . gan cookie = 0
		    	if($cokie)
		    	{
		    		if(Mage::helper('affiliate')->getLockAffiliate($cokie)== 1) {
		    			$cokie = 0;
		    		}
		    	}
		    	else
		    	{
		    		$cokie = 0;
		    	};
		    	$invitation_type = MW_Affiliate_Model_Typeinvitation::NON_REFERRAL;
		    	if($cokie != 0) {
		    		$invitation_type = MW_Affiliate_Model_Typeinvitation::REFERRAL_LINK;
		    	}
		    	
		    	// Save affiliate customers to db
	        	$customerData = array(
	        						  'customer_id'			=> $customer_id,
		              				  'active'				=> $active,
		              				  'payment_gateway'		=> $getway_withdrawn,
		              				  'payment_email'		=> $payment_email,
		              				  'auto_withdrawn'		=> $auto_withdrawn,
		              				  'withdrawn_level'		=> $payment_release_level,
	    							  'reserve_level'		=> $reserve_level,
	        	                      'bank_name'			=> $bank_name,
							    	  'name_account'		=> $name_account,
							    	  'bank_country'		=> $bank_country,
							    	  'swift_bic'			=> $swift_bic,
							    	  'account_number'		=> $account_number,
							    	  're_account_number'	=> $re_account_number,
							    	  //'referral_site'		=> $referral_site,
	    							  'total_commission'	=> 0,
	    							  'total_paid'			=> 0,
	        						  'referral_code'		=> '',
	    							  'status'				=> 1,
	        						  'invitation_type'		=> $invitation_type,
	        						  'customer_time' 		=> now(),
		              				  'customer_invited'	=> $cokie
	        					);
	        	Mage::getModel('affiliate/affiliatecustomers')->saveCustomerAccount($customerData);
	        	
	        	// Save affiliate websites to db
	        	if($referral_site) {
	        		$sites = explode(',', $referral_site);
	        		$websiteModel = Mage::getModel('affiliate/affiliatewebsitemember'); 
	        		foreach($sites as $url) {
	        			$siteItem = array(
	        							'customer_id'   => $customer_id,
	        							'domain_name'	=> trim($url),	
	        							'verified_key'  => Mage::helper('affiliate')->getWebsiteVerificationKey(trim($url)),
	        							'status'		=> MW_Affiliate_Model_Statuswebsite::UNVERIFIED
	        						);
	        			$websiteModel->setData($siteItem);
	        			$websiteModel->save();
	        		}
	        	}
	        	
	        	// update affiliate invitation
	        	$clientIP = Mage::app()->getRequest()->getServer('REMOTE_ADDR');
	        	$referral_from = Mage::getModel('core/cookie')->get('mw_referral_from');
	    		$referral_to = Mage::getModel('core/cookie')->get('mw_referral_to');
	    		$referral_from_domain = Mage::getModel('core/cookie')->get('mw_referral_from_domain');
	    		
	    		if(!$referral_from) {
	    			$referral_from = '';
	    		}
	    		if(!$referral_to) {
	    			$referral_to = '';
	    		}
	    		if(!$referral_from_domain) {
	    			$referral_from_domain = '';
	    		}
	    		if($cokie != 0) { 
	    			Mage::helper('affiliate')->updateAffiliateInvitionNew($customer_id, $cokie, $clientIP,$referral_from,$referral_from_domain,$referral_to,$invitation_type);
	    		}
            }
	   		
	    	// xoa session di
	    	$session->unsetData('check_affiliate');
	        $session->unsetData('payment_gateway');
	        $session->unsetData('payment_email');
	        $session->unsetData('auto_withdrawn');
	        $session->unsetData('withdrawn_level');
	        $session->unsetData('reserve_level');
	        $session->unsetData('bank_name');
	        $session->unsetData('name_account');
	        $session->unsetData('bank_country');
	        $session->unsetData('swift_bic');
	        $session->unsetData('referral_site');
    		
	        if($active == MW_Affiliate_Model_Statusactive::PENDING)
    		{
    			// gui mail cho khach hang khi dang ky lam thanh vien affiliate 
		    	Mage::helper('affiliate') ->sendEmailCustomerPending($customer_id);
	    		// gui mail cho admin chiu trach nhiem active customer affiliate
	    		Mage::helper('affiliate') ->sendEmailAdminActiveAffiliate($customer_id);
    		}
    		else if($active == MW_Affiliate_Model_Statusactive::ACTIVE)
    		{
    			// set lai referral code cho cac customer affiliate
                Mage::helper('affiliate')->setReferralCode($customer_id);
    			$store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
	        	Mage::helper('affiliate')->setMemberDefaultGroupAffiliate($customer_id,$store_id);
                //gui mail khi admin dong y cho gia nhap vao affiliate
                Mage::helper('affiliate')->sendMailCustomerActiveAffiliate($customer_id);
    		}
	    	
	    	$this->_getSession()->addSuccess($this->__("You have successfully saved affiliate account"));
	    	$this->_redirect('affiliate/index/referralaccount/');				
    	}
    	else 
    	{
    		$this->_redirect('affiliate/index/createaccount/');
            return;
    	}
    }
    
	public function transactionAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate') ->getAffiliateActive();
		$lock = Mage::helper('affiliate') ->getAffiliateLock();
		if($active > 0)
		{   
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
			$this->loadLayout(); 
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('core/session');    
			$this->renderLayout();
		}
    	else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		}				
    }
    
	public function withdrawnAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate') ->getAffiliateActive();
		$lock = Mage::helper('affiliate') ->getAffiliateLock();
		if($active > 0)
		{   
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
			$this->loadLayout(); 
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('core/session');    
			$this->renderLayout();
		}
    	else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		}				
    }
    
	public function withdrawnpostAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
    	if ($this->getRequest()->isPost())
		{   
			$rate = Mage::helper('core')->currency(1,false);
			$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
	  		$customer_withdrawn = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
	  		$customer_credit = Mage::getModel('credit/creditcustomer')->load($customer_id);
	  		$reserve_level = $customer_withdrawn ->getReserveLevel();
	  		$balance = $customer_credit->getCredit();
			$max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
			$min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
			$post = $this->getRequest()->getPost();
			$withdraw_amount = (double)$post["withdraw_amount"];

			// convert sang gia base
			$withdraw_amount = $withdraw_amount/$rate;
			if($withdraw_amount >= $min && $withdraw_amount <= $max && $withdraw_amount + $reserve_level <= $balance)
			{   
				$this->loadLayout();  
				$this->renderLayout();
			}
			else if($withdraw_amount + $reserve_level > $balance)
			{   
				$this->_getSession()->addError($this->__("Your balance is not enough for withdrawing"));
				$this->_redirect('affiliate/index/withdrawn/');
			}
			else if(($withdraw_amount < $min) || ($withdraw_amount > $max))
			{   
				$this->_getSession()->addError($this->__("Your requested amount does not match the condition"));
				$this->_redirect('affiliate/index/withdrawn/');
			}
		}			
    }
    
    public function withdrawnsubmitAction()
	{
		if ($this->getRequest()->isPost())
		{
			$store_id = Mage::app()->getStore()->getId();
			$customer = Mage::getSingleton("customer/session")->getCustomer();
			$credit_customer = Mage::getModel('credit/creditcustomer')->load($customer->getId());
			$affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer->getId());
			$post = $this->getRequest()->getPost();
			$your_balance = doubleval($credit_customer->getCredit());
			$withdrawn_amount = doubleval($post["withdraw_amount"]);

            $fee = Mage::helper('affiliate/data')->getFeePaymentGateway($affiliate_customer ->getPaymentGateway(),$store_id);
			if(strpos($fee, '%')) {
		    	$percent = doubleval(substr($fee, 0, strpos($fee, '%')));
		    	$fee = ($percent * $withdrawn_amount)/100;

		    } else {
		    	$fee = doubleval($fee);
		    }
			
			$withdrawn_receive = $withdrawn_amount - $fee;
			$customerid = (int)$customer->getId();
			
			$affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customerid);
			$payment_gateway = $affiliate_customer->getPaymentGateway();
			$payment_email = $affiliate_customer->getPaymentEmail();
			
			/*
			if($payment_gateway == 'paypal') {
				$paypalParams = array(
									'amount' 		=> $withdrawn_receive,
									'currency'  	=> 'USD',
									'customer_email'=> 'ducnm0990@mailinator.com',
									'customer_name'	=> $customer->getName(),
								);
				
				$paypalResponse = Mage::helper('affiliate')->withdrawnPaypal($paypalParams);
				
				if($paypalResponse['status'] !== 'success') {
					$this->_getSession()->addError($this->__($paypalResponse['error']));
					$this->_redirect('affiliate/index/withdrawn/');
				}
			}
			*/
			
			if($payment_gateway == 'banktransfer') {
				$payment_email = '';
			}
		    	
		  	$bank_name 		= $affiliate_customer->getBankName();
			$name_account 	= $affiliate_customer->getNameAccount();
			$bank_country 	= $affiliate_customer->getBankCountry();
			$swift_bic 		= $affiliate_customer->getSwiftBic();
			$account_number	= $affiliate_customer->getAccountNumber();
			$re_account_number = $affiliate_customer->getReAccountNumber();
						
			$withData  =  array(
								'customer_id'		=> $customerid,
								'payment_gateway'	=> $payment_gateway,
	              				'payment_email'		=> $payment_email,
	              				'bank_name'			=> $bank_name,
					    	    'name_account'		=> $name_account,
					    	    'bank_country'		=> $bank_country,
					    	    'swift_bic'			=> $swift_bic,
					    	    'account_number'	=> $account_number,
					    	    're_account_number'	=> $re_account_number,
              					'withdrawn_amount'	=> $withdrawn_amount,
              					'fee'				=> $fee,
								'amount_receive'	=> $withdrawn_receive,
          						'status'			=> MW_Affiliate_Model_Status::PENDING,
                        		'withdrawn_time'	=> now()
						   );
			Mage::getModel('affiliate/affiliatewithdrawn')->setData($withData)->save(); 
			
			/* Update affiliate customer table (total_paid) */
			$affiliateCustomerModel = Mage::getModel('affiliate/affiliatecustomers')->load($customerid);
			$oldTotalPaid = $affiliateCustomerModel->getTotalPaid();
			$newTotalPaid = $oldTotalPaid + $withdrawn_amount;
			$newTotalPaid = round($newTotalPaid, 2);
			$affiliateCustomerModel->setData('total_paid', $newTotalPaid)->save();
				
			/* Update credit customer table */
			$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customerid);
    		$oldCredit = $creditcustomer->getCredit();
   			$amount = -$withdrawn_amount;
  			$newCredit = $oldCredit + $amount;
  			$newCredit = round($newCredit,2);
   			$creditcustomer->setCredit($newCredit)->save();
   			
   			$collectionWithdrawns = Mage::getModel('affiliate/affiliatewithdrawn')
   									->getCollection()
		                    		->addFieldToFilter('customer_id',$customerid)
	                    			->setOrder('withdrawn_id','DESC');
	        foreach($collectionWithdrawns as $collectionWithdrawn)
	        {
	        	$withdrawn_id=$collectionWithdrawn->getWithdrawnId();
	        	break;
	        }
   			
       		$historyData = array(
       							 'customer_id'			=> $customerid,
			 					 'type_transaction'		=> MW_Credit_Model_Transactiontype::WITHDRAWN, 
			 					 'status'				=> MW_Credit_Model_Orderstatus::PENDING,
				     		     'transaction_detail'	=> $withdrawn_id, 
				           		 'amount'				=> $amount, 
				         		 'beginning_transaction'=> $oldCredit,
				        		 'end_transaction'		=> $newCredit,
				           	     'created_time'			=> now()
       						);
   			Mage::getModel("credit/credithistory")->setData($historyData)->save();
   
   			$withdrawn_amount_currency = Mage::helper('core')->currency($withdrawn_amount,true,false);
   			
   			// gui mail cho khach hang khi withdrawn bang tay
   			Mage::helper('affiliate')->sendMailCustomerRequestWithdrawn($customerid, $withdrawn_amount);
		    			
			$this->_getSession()->addSuccess($this->__("You have requested to withdraw: %s",$withdrawn_amount_currency));
			$this->_redirect('affiliate/index/withdrawn/');
		}
	}
	
	public function accountAction()
    {   
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$this->loadLayout(); 
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('core/session');    
		$this->renderLayout();
    }
   
    public function affiliatenetworkAction() {
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
    		$this->_initLayoutMessages('core/session');
    		$this->renderLayout();
    	}
    	else if($active == 0)
    	{
    		$this->_redirect('affiliate/index/createaccount/');
    	}
    }
    
    public function ajaxcheckwebsiteAction() {
    	if($this->getRequest()->isXmlHttpRequest()) {
    		$this->_redirect(Mage::getBaseUrl());
    	}
    	
    	$referralSite = $this->getRequest()->getParam('referral_site');
    	
    	$sites = explode(',', $referralSite);
    	foreach($sites as $site) {
    		$site = trim($site);
    		
    		/* Check if website is valid format */
    		if(!Mage::helper('affiliate')->validateDomainUrl($site)) {
    			echo Zend_Json::encode(array(
    										'status'  => 'error',
    										'type'	  => 'domain-name',
    										'message' => 'The website ' . $site . ' is not valid' 
    								   ));
    			die;
    		}
    		
    		/* Check if website is available or not */
    		$isExisted = Mage::getModel('affiliate/affiliatewebsitemember')
    					 ->getCollection()
    					 ->addFieldToFilter('domain_name', array('eq' => $site));

    		if(sizeof($isExisted) > 0) {
    			echo Zend_Json::encode(array(
    										'status'  => 'error',
    										'type'	  => 'domain-existed',
    										'message' => 'The website ' . $site . ' is registered by another affiliate' 
    								   ));
    			die;
    		}
    	}
    	
    	/* No error - website passed */
    	echo Zend_Json::encode(array('status' => 'success'));
    	die;
    }
    
    public function ajaxgetfeeAction(){
        if($this->getRequest()->getPost('ajax') == 'true'){
            $data = $this->getRequest()->getPost('gateway');
            print json_encode(Mage::helper('affiliate')->getFeePaymentGateway($data));
            exit;
        }
    }
}
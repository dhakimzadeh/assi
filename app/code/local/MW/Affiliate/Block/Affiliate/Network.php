<?php
class MW_Affiliate_Block_Affiliate_Network extends Mage_Core_Block_Template
{
	protected $array_network = array();
	protected $array_network_table = array();
	protected $_arrayResult = array();
	
	public function __construct()
    {
        parent::__construct();
        
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
       	$this->showSubAffiliateNetworkTable($customer_id,'',0);
       
       	$collection = new Varien_Data_Collection();
       	$i = 1;
       	foreach ($this->_arrayResult as $row) {
       		$rowObj = new Varien_Object();
		    $rowObj->setData($row);
		    $collection->addItem($rowObj);
		    $i = $i + 1;
       	}
		
    	$this->setAffiliateNetworkCollection($collection);
    }
    
	public function getAffiliateParents($customer_id)
	{   
		$result = array();
		if($customer_id) {
			$collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
									->addFieldToFilter('customer_invited',$customer_id)
									->addFieldToFilter('status',MW_Affiliate_Model_Statusreferral::ENABLED)
									->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
			
			$result = array_diff($collection->getAllIds(),array($customer_id));
		}						
		return $result;
	}
	
	public function getSizeAffiliateNetwork()
	{   
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		if($customer_id) {
			return sizeof($this->getAffiliateParents($customer_id));
		} 
		else return 0;
	}
	
	public function showSubAffiliateNetwork($customer_id)
	{   
		$result = '';
		if(!in_array($customer_id, $this->array_network)) {
		    $this->array_network[] = $customer_id;
		
			$size = sizeof($this->getAffiliateParents($customer_id));
			$customer_childs = $this->getAffiliateParents($customer_id);
			$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
			
			$result = '<li style="padding-left:20px;font-weight:bold; list-style:inside;">' . $name;
			if($size > 0) {
				$result .= '<ul>';
				foreach ($customer_childs as $customer_child) {
					$result .= $this->showSubAffiliateNetwork($customer_child);
				}
				$result .= '</ul>';
			}
			$result .= '</li>';
		}
		return $result;
	}
	
	public function showSubAffiliateNetworkTable($customer_id, $referral, $i)
	{   
		if(!in_array($customer_id, $this->array_network_table)) {
		    $this->array_network_table[] = $customer_id;
		
			$size = sizeof($this->getAffiliateParents($customer_id));
			$customer_childs = $this->getAffiliateParents($customer_id);
			$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
			$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
			
			$affiliateCustomer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id); 
			$customerCommission = $affiliateCustomer->getTotalCommission();
			$customerJoinedDate = $affiliateCustomer->getCustomerTime();
			$customerStatus		= $affiliateCustomer->getActive(); 
			
			$statusOptions = MW_Affiliate_Model_Statusactive::getOptionArray();
			if($referral != '') {
				$this->_arrayResult[] = array(
											'level'			=> $i, 
											'name'			=> $name,
											'email'			=> $email,
											'referral'		=> $referral,
											'commission'	=> $customerCommission,
											'joined_date'	=> $customerJoinedDate,
											'status'		=> $statusOptions[$customerStatus]
										);
			}
			
			if($size > 0) {
				$i = $i + 1 ;
				foreach($customer_childs as $customer_child) {
					$this->showSubAffiliateNetworkTable($customer_child,$name, $i);
				}
			}
		}
	}
	public function showNumberArrows($level)
	{
		$arrow = $image_arrow = '';
		$package_url = Mage::getSingleton('core/design_package')->getPackageName();
		$theme_url = Mage::getSingleton('core/design_package')->getTheme('frontend');
		
		for($i=2; $i<=(int)$level;$i++)
		{
			if($i==2)
				$image_arrow = 'mw_affiliate/images/line.gif';
			if($i>2)
				$image_arrow = 'mw_affiliate/images/line2.gif';
			$img_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/'.$package_url.'/'.$theme_url.'/'.$image_arrow;
			$arrow.= "<img src='".$img_url."' />";
		}
		return $arrow;
	}

	public function _prepareLayout()
    {
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_affiliate_network')
					  ->setCollection($this->getAffiliateNetworkCollection());	
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        
        return $this;
    }
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
 	public function getCollection()
    {
    	return $this->getChild("pager")->getCollection();
    }
}
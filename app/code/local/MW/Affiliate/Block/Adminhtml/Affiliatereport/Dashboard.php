<?php

/**
 * @author Tuanlv
 * @copyright 2014
 */
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Dashboard extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
	    $this->_controller = 'adminhtml_affiliatereport_dashboard';
	    $this->_headerText = Mage::helper('affiliate')->__('Dashboard');
	    $this->_blockGroup = 'affiliate';
	    parent::__construct();
	    $this->_removeButton('add');
  }
  
}
?>
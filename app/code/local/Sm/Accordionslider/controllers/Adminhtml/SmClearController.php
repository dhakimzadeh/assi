<?php

/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Adminhtml_SmClearController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
		$folder_cache = Mage::getBaseDir('cache');
		$folder_cache = $folder_cache.'/Sm/';
		if(file_exists($folder_cache))
        {
			mageDelTree($folder_cache);
			Mage::getSingleton('adminhtml/session')->addSuccess('Flush SM Cache successfully.');
            $this->_redirect('adminhtml/cache/index');
        }
        else
        {
            $this->_redirect('adminhtml/cache/index');
        }
    }
	
    protected function _isAllowed()
    {
        return true;
    }
}

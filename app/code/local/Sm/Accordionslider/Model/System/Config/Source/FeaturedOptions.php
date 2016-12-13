<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_FeaturedOptions
{
	const SHOW = 0;
	const HIDE = 1;
	const ONLY = 2;

	public function toOptionArray()
	{
		$opt = new Varien_Object(array(
			self::SHOW => Mage::helper('accordionslider')->__('Show'),
			self::HIDE => Mage::helper('accordionslider')->__('Hide'),
			self::ONLY => Mage::helper('accordionslider')->__('Only'),
		));
		return $opt->getData();
	}
}
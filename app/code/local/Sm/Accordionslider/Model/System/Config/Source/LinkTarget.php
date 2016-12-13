<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_LinkTarget
{
	const SAMPLE_WINDOW = '_self';
	const NEW_WINDOW = '_blank';
	const POPUP_WINDOW = '_windowopen';

	public function toOptionArray()
	{
		$option = new Varien_Object(array(
			self::SAMPLE_WINDOW => Mage::helper('accordionslider')->__('Sample Window'),
			self::NEW_WINDOW => Mage::helper('accordionslider')->__('New Window'),
			self::POPUP_WINDOW => Mage::helper('accordionslider')->__('Popup Window')
		));
		return $option->getData();
	}
}
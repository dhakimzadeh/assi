<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_EventTrigger
{
	const CLICK = 'click';
	const DBCLICK = 'dblclick';
	const MOUSEOVER = 'mouseover';
	const MOUSEOUT = 'mouseout';

	public function toOptionArray()
	{
		$option = new Varien_Object(array(
			self::CLICK => Mage::helper('accordionslider')->__('Click'),
			self::DBCLICK => Mage::helper('accordionslider')->__('Double Click'),
			self::MOUSEOVER => Mage::helper('accordionslider')->__('Mouse Over'),
			self::MOUSEOUT => Mage::helper('accordionslider')->__('Mouse Out')
		));
		return $option->getData();
	}
}
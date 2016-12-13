<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_TextTransform
{
	const NONE  = 'none';
	const CAPITALIZE = 'capitalize';
	const UPPERCASE = 'uppercase';
	const LOWERCASE = 'lowercase';
	const INHERIT = 'inherit';

	public function toOptionArray()
	{
		$opt = new Varien_Object(array(
			self::NONE => Mage::helper('accordionslider')->__('None'),
			self::CAPITALIZE => Mage::helper('accordionslider')->__('Capitalize'),
			self::UPPERCASE => Mage::helper('accordionslider')->__('Uppercase'),
			self::LOWERCASE => Mage::helper('accordionslider')->__('Right'),
			self::INHERIT => Mage::helper('accordionslider')->__('Right')
		));
		return $opt->getData();
	}
}
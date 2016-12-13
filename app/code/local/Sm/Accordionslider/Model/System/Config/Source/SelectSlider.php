<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_SelectSlider
{
	const STYLE1 ='simple';
	const STYLE2 ='advanced1';
	const STYLE3 ='advanced2';

	public function toOptionArray()
	{
		$opt = new Varien_Object(array(
			self::STYLE1 => Mage::helper('accordionslider')->__('Type 1'),
			self::STYLE2 => Mage::helper('accordionslider')->__('Type 2'),
			self::STYLE3 => Mage::helper('accordionslider')->__('Type 3'),
		));
		return $opt->getData();
	}
}

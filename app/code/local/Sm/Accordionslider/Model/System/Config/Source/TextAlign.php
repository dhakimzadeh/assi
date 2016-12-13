<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_TextAlign
{
	const LEFT = 'left';
	const CENTER = 'center';
	const RIGHT = 'right';

	public function toOptionArray()
	{
		$opt = new Varien_Object(array(
			self::LEFT => Mage::helper('accordionslider')->__('Left'),
			self::CENTER => Mage::helper('accordionslider')->__('Center'),
			self::RIGHT => Mage::helper('accordionslider')->__('Right'),
		));
		return $opt->getData();
	}
}
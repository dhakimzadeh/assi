<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_IncludeExclude
{

	const INCLUD = 1;
	const EXCLUD = 0;

	public function toOptionArray()
	{
		$option = new Varien_Object(array(
			self::INCLUD => Mage::helper('accordionslider')->__('Include'),
			self::EXCLUD => Mage::helper('accordionslider')->__('Exclude')
		));
		return $option->getData();
	}}
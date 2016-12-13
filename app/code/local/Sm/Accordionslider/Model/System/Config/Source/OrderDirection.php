<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_OrderDirection
{
	const ASC = 'ASC';
	const DESC = 'DESC';

	public function toOptionArray()
	{
		$opt = new Varien_Object(array(
			self::ASC => Mage::helper('accordionslider')->__('Asc'),
			self::DESC => Mage::helper('accordionslider')->__('Desc')
		));
		return $opt->getData();
	}
}
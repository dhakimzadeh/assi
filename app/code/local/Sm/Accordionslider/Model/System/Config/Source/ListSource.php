<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_ListSource
{
	const CATALOG = 'catalog';
	const MEDIA = 'media';
	const IDS = 'ids';

	public function toOptionArray()
	{
		$opt = new Varien_Object(array(
			self::CATALOG => Mage::helper('accordionslider')->__('Catalog'),
			self::MEDIA => Mage::helper('accordionslider')->__('Media'),
			self::IDS => Mage::helper('accordionslider')->__('Product IDs to Exclude')
		));
		return $opt->getData();
	}
}
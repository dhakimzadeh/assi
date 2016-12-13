<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_OrderBy
{
	const NAME          = 'name';
	const ENTITY_ID     = 'entity_id';
	const POSITION      = 'position';
	const CREATED_AT    = 'created_at';
	const PRICE         = 'price';
	const LASTED_PRODUCT = 'lasted_product';
	const TOP_RATING    = 'top_rating';
	const MOST_REVIEWED = 'most_reviewed';
	const MOST_VIEWED   = 'most_viewed';
	const BEST_SALES    = 'best_sales';
	const RAMDOM        = 'random';

	public function toOptionArray()
	{
		$option = new Varien_Object(array(
			self::NAME => Mage::helper('accordionslider')->__('Name'),
			self::ENTITY_ID => Mage::helper('accordionslider')->__('Id'),
			self::POSITION => Mage::helper('accordionslider')->__('Position'),
			self::CREATED_AT => Mage::helper('accordionslider')->__('Date Created'),
			self::PRICE => Mage::helper('accordionslider')->__('Price'),
			self::LASTED_PRODUCT => Mage::helper('accordionslider')->__('Lasted Product'),
			self::TOP_RATING => Mage::helper('accordionslider')->__('Top Rating'),
			self::MOST_REVIEWED => Mage::helper('accordionslider')->__('Most Reviews'),
			self::MOST_VIEWED => Mage::helper('accordionslider')->__('Most Views'),
			self::BEST_SALES => Mage::helper('accordionslider')->__('Most Selling'),
			self::RAMDOM => Mage::helper('accordionslider')->__('Random')
		));
		return $option->getData();
	}
}
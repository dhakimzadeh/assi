<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_Effect
{
	const NONE = 'none';
	const FADEIN = 'fadeIn';
	const ZOOMIN = 'zoomIn';
	const ZOOMOUT = 'zoomOut';
	const SLIDELEFT = 'slideLeft';
	const SLIDERIGHT = 'slideRight';
	const SLIDETOP = 'slideTop';
	const SLIDEBOTTOM = 'slideBottom';
	const FLIP = 'flip';
	const FLIPINX = 'flipInX';
	const FLIPINY = 'flipInY';
	const BOUNCEIN = 'bounceIn';
	const BOUNCEINUP = 'bounceInUp';
	const BOUNCEINDOWN = 'bounceInDown';
	const PAGETOP = 'pageTop';
	const PAGEBOTTOM = 'pageBottom';
	const STARWARS = 'starwars';

	public function toOptionArray()
	{
		$option = new Varien_Object(array(
			self::NONE => Mage::helper('accordionslider')->__('None'),
			self::FADEIN => Mage::helper('accordionslider')->__('Fade In'),
			self::ZOOMIN => Mage::helper('accordionslider')->__('Zoom In'),
			self::ZOOMOUT => Mage::helper('accordionslider')->__('Zoom Out'),
			self::SLIDELEFT => Mage::helper('accordionslider')->__('Slide Left'),
			self::SLIDERIGHT => Mage::helper('accordionslider')->__('Slide Right'),
			self::SLIDETOP => Mage::helper('accordionslider')->__('Slide Top'),
			self::SLIDEBOTTOM => Mage::helper('accordionslider')->__('Slide Bottom'),
			self::FLIP => Mage::helper('accordionslider')->__('Flip'),
			self::FLIPINX => Mage::helper('accordionslider')->__('Flip In Horizontal'),
			self::FLIPINY => Mage::helper('accordionslider')->__('Flip In Vertical'),
			self::BOUNCEIN => Mage::helper('accordionslider')->__('Bounce In'),
			self::BOUNCEINUP => Mage::helper('accordionslider')->__('Bounce In Up'),
			self::BOUNCEINDOWN => Mage::helper('accordionslider')->__('Bounce In Down'),
			self::PAGETOP => Mage::helper('accordionslider')->__('Page Top'),
			self::PAGEBOTTOM => Mage::helper('accordionslider')->__('Page Bottom'),
			self::STARWARS => Mage::helper('accordionslider')->__('Star Wars')
		));
		return $option->getData();
	}
}
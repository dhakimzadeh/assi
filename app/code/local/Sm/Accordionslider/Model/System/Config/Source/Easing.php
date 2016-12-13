<?php
/*------------------------------------------------------------------------
 # SM Accordion Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_Accordionslider_Model_System_Config_Source_Easing
{
	const EASING_1      = 'linear';
	const EASING_2      = 'swing';
	const EASING_3      = 'easeInQuad';
	const EASING_4      = 'easeOutQuad';
	const EASING_5      = 'easeInOutQuad';
	const EASING_6      = 'easeInCubic';
	const EASING_7      = 'easeOutCubic';
	const EASING_8      = 'easeInOutCubic';
	const EASING_9      = 'easeInQuart';
	const EASING_10     = 'easeOutQuart';
	const EASING_11     = 'easeInOutQuart';
	const EASING_12     = 'easeInQuint';
	const EASING_13     = 'easeOutQuint';
	const EASING_14     = 'easeInOutQuint';
	const EASING_15     = 'easeInExpo';
	const EASING_16     = 'easeOutExpo';
	const EASING_17     = 'easeInOutExpo';
	const EASING_18     = 'easeInSine';
	const EASING_19     = 'easeOutSine';
	const EASING_20     = 'easeInOutSine';
	const EASING_21     = 'easeInCirc';
	const EASING_22     = 'easeOutCirc';
	const EASING_23     = 'easeInOutCirc';
	const EASING_24     = 'easeInElastic';
	const EASING_25     = 'easeOutElastic';
	const EASING_26     = 'easeInOutElastic';
	const EASING_27     = 'easeInBack';
	const EASING_28     = 'easeOutBack';
	const EASING_29     = 'easeInOutBack';
	const EASING_30     = 'easeInBounce';
	const EASING_31     = 'easeOutBounce';
	const EASING_32     = 'easeInOutBounce';

	public function toOptionArray(){
		$opt = new Varien_Object(array(
			self::EASING_1 => Mage::helper('accordionslider')->__('Linear'),
			self::EASING_2 => Mage::helper('accordionslider')->__('Swing'),
			self::EASING_3 => Mage::helper('accordionslider')->__('EaseInQuad'),
			self::EASING_4 => Mage::helper('accordionslider')->__('EaseOutQuad'),
			self::EASING_5 => Mage::helper('accordionslider')->__('EaseInOutQuad'),
			self::EASING_6 => Mage::helper('accordionslider')->__('EaseInCubic'),
			self::EASING_7 => Mage::helper('accordionslider')->__('EaseOutCubic'),
			self::EASING_8 => Mage::helper('accordionslider')->__('EaseInOutCubic'),
			self::EASING_9 => Mage::helper('accordionslider')->__('EaseInQuart'),
			self::EASING_10 => Mage::helper('accordionslider')->__('EaseOutQuart'),
			self::EASING_11 => Mage::helper('accordionslider')->__('EaseInOutQuart'),
			self::EASING_12 => Mage::helper('accordionslider')->__('EaseInQuint'),
			self::EASING_13 => Mage::helper('accordionslider')->__('EaseOutQuint'),
			self::EASING_14 => Mage::helper('accordionslider')->__('EaseInOutQuint'),
			self::EASING_15 => Mage::helper('accordionslider')->__('EaseInExpo'),
			self::EASING_16 => Mage::helper('accordionslider')->__('EaseOutExpo'),
			self::EASING_17 => Mage::helper('accordionslider')->__('EaseInOutExpo'),
			self::EASING_18 => Mage::helper('accordionslider')->__('EaseInSine'),
			self::EASING_19 => Mage::helper('accordionslider')->__('EaseOutSine'),
			self::EASING_20 => Mage::helper('accordionslider')->__('EaseInOutSine'),
			self::EASING_21 => Mage::helper('accordionslider')->__('EaseInCirc'),
			self::EASING_22 => Mage::helper('accordionslider')->__('EaseOutCirc'),
			self::EASING_23 => Mage::helper('accordionslider')->__('EaseInOutCirc'),
			self::EASING_24 => Mage::helper('accordionslider')->__('EaseInElastic'),
			self::EASING_25 => Mage::helper('accordionslider')->__('EaseOutElastic'),
			self::EASING_26 => Mage::helper('accordionslider')->__('EaseInOutElastic'),
			self::EASING_27 => Mage::helper('accordionslider')->__('EaseInBack'),
			self::EASING_28 => Mage::helper('accordionslider')->__('EaseOutBack'),
			self::EASING_29 => Mage::helper('accordionslider')->__('EaseInOutBack'),
			self::EASING_30 => Mage::helper('accordionslider')->__('EaseInBounce'),
			self::EASING_31 => Mage::helper('accordionslider')->__('EaseOutBounce'),
			self::EASING_32 => Mage::helper('accordionslider')->__('EaseInOutBounce'),
		));
		return $opt->getData();
	}
}
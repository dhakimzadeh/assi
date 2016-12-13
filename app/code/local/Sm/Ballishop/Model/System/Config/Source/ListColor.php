<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Ballishop_Model_System_Config_Source_ListColor
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'red', 'label'=>Mage::helper('ballishop')->__('Red')),
		array('value'=>'blue', 'label'=>Mage::helper('ballishop')->__('Blue')),
		array('value'=>'pink', 'label'=>Mage::helper('ballishop')->__('Pink')),
		array('value'=>'green', 'label'=>Mage::helper('ballishop')->__('Green')),
		array('value'=>'violet', 'label'=>Mage::helper('ballishop')->__('Violet'))
		);
	}
}

<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Ballishop_Model_System_Config_Source_ListBodyFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'Arial', 'label'=>Mage::helper('ballishop')->__('Arial')),
			array('value'=>'Arial Black', 'label'=>Mage::helper('ballishop')->__('Arial-black')),
			array('value'=>'Courier New', 'label'=>Mage::helper('ballishop')->__('Courier New')),
			array('value'=>'Georgia', 'label'=>Mage::helper('ballishop')->__('Georgia')),
			array('value'=>'Tahoma', 'label'=>Mage::helper('ballishop')->__('Tahoma')),
			array('value'=>'Times New Roman', 'label'=>Mage::helper('ballishop')->__('Times New Roman')),
			array('value'=>'Trebuchet', 'label'=>Mage::helper('ballishop')->__('Trebuchet')),
			array('value'=>'Verdana', 'label'=>Mage::helper('ballishop')->__('Verdana')),
			array('value'=>'maingooglefont', 'label'=>Mage::helper('ballishop')->__('Google Font'))
		);
	}
}

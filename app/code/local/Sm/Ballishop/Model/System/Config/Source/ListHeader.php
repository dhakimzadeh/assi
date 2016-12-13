<?php
/*------------------------------------------------------------------------
 # SM Ballishop - Version 1.1
 # Copyright (c) 2013 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Ballishop_Model_System_Config_Source_ListHeader
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'1', 'label'=>Mage::helper('ballishop')->__('Header 1')),
			array('value'=>'2', 'label'=>Mage::helper('ballishop')->__('Header 2'))
		);
	}
}

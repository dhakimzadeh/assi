<?php
/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Cartpro_Model_System_Config_Source_ListEffectSelector
{
	public function toOptionArray()
	{
		return array(
				array('value'=>'click', 'label'=>Mage::helper('cartpro')->__('Click')),
				array('value'=>'hover', 'label'=>Mage::helper('cartpro')->__('Hover'))
		);
	}
}

<?php
/*------------------------------------------------------------------------
 # SM Listing Tabs - Version 2.0.1
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Listingtabs_Model_System_Config_Source_TrueFalse
{
    public function toOptionArray()
    {
        return array(
            array('value' => true, 'label' => Mage::helper('listingtabs')->__('True')),
            array('value' => false, 'label' => Mage::helper('listingtabs')->__('False')),
        );
    }
}

<?php
/*------------------------------------------------------------------------
 # SM Listing Tabs - Version 2.0.1
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Listingtabs_Model_System_Config_Source_ShowLoadmoreSlider
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'loadmore', 'label'        => Mage::helper('listingtabs')->__('Loadmore')),
            array('value' => 'slider', 'label'          => Mage::helper('listingtabs')->__('Slider')),
        );
    }
}

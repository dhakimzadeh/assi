<?php
/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Cartpro_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('cartpro')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('cartpro')->__('Disabled')
        );
    }
}
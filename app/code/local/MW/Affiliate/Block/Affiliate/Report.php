<?php

/**
 * @author Tuanlv
 * @copyright 2014
 */
class MW_Affiliate_Block_Affiliate_Report extends Mage_Core_Block_Template
{
    public function getId(){
        return (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
    }
}
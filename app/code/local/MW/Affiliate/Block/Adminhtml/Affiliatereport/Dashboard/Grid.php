<?php

/**
 * @author Tuanlv
 * @copyright 2014
 */
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Dashboard_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
     /**
     * Initialize Grid settings
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('mw_affiliate/report/dashboard.phtml');
    }

    public function getWebsiteCollection()
    {
        return Mage::app()->getWebsites();
    }

    public function getGroupCollection(Mage_Core_Model_Website $website)
    {
        return $website->getGroups();
    }

    public function getStoreCollection(Mage_Core_Model_Store_Group $group)
    {
        return $group->getStores();
    }

    /**
     * Return store switcher hint html
     *
     * @return string
     */
    public function getHintHtml()
    {
        $html = '';
        $url = $this->getHintUrl();
        if ($url) {
            $html = '<a'
                . ' href="'. $this->escapeUrl($url) . '"'
                . ' onclick="this.target=\'_blank\'"'
                . ' title="' . $this->__('What is this?') . '"'
                . ' class="link-store-scope">'
                . $this->__('What is this?')
                . '</a>';
        }
        return $html;
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/overview', array('_current'=>true, 'period'=>null));
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store');
        return intval($storeId);
    }
    
    public function getMostUserSales()
    {
        return Mage::getModel('affiliate/report')->prepareCollectionMostUserSales();
    }
}
?>
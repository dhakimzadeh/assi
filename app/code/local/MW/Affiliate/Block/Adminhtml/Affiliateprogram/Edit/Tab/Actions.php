<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
//class MW_Test_Block_Adminhtml_Test_Edit_Tab_Conditions
   // extends Mage_Adminhtml_Block_Widget_Form
    //implements Mage_Adminhtml_Block_Widget_Tab_Interface
class MW_Affiliate_Block_Adminhtml_Affiliateprogram_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
//    public function getTabLabel()
//    {
//        return Mage::helper('salesrule')->__('Actions');
//    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
//    public function getTabTitle()
//    {
//        return Mage::helper('salesrule')->__('Actions');
//    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
//    public function canShowTab()
//    {
//        return true;
//    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
//    public function isHidden()
//    {
//        return false;
//    }

    protected function _prepareForm()
    {
		$model = Mage::registry('affiliate_data_program');
		//$model = Mage::getModel('salesrule/rule');
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');
 
		$fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('affiliate')->__('Calculate commission and discount as follows:')));
		
		$fieldset->addField('commission', 'text', array(
          'label'     => Mage::helper('affiliate')->__('Affiliate Commission (if referred customer meets program conditions)'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'commission',
      	  'note' => Mage::helper('affiliate')->__('Format x or y% (x - fixed commission amount/ y% - percent of product value) separate multi-level marketing with commas for each level (ex 10%, 5%, 1 etc.)'),
      	));
       $fieldset->addField('discount', 'text', array(
          'label'     => Mage::helper('affiliate')->__('Referred Customer Discount for Subsequent Purchases'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'discount',
       	  'note' => Mage::helper('affiliate')->__('To change discounts (fixed amount \'x\' or percentage \'y%\') for subsequent customer orders, separate discounts with commas in field. Last figure will apply for all subsequent purchases. Ex 10%,5,0 for 10% discount on 1st purchase, $5 discount on 2nd purchase and no discount on subsequent purchases (until expiration date set in Affiliate Group detail)'),
      	));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newActionHtml/form/rule_actions_fieldset'));
		//echo $this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'); 
		
        $fieldset = $form->addFieldset('actions_fieldset', array(
            'legend'=>Mage::helper('affiliate')->__('Affiliate will earn commission on individual cart items if they meet the following conditions (leave blank for all items)')
        ))->setRenderer($renderer);

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => Mage::helper('affiliate')->__('Apply To'),
            'title' => Mage::helper('affiliate')->__('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));
        
       $form->setValues($model->getData());
		
        //$form->setUseContainer(true);
		$fieldset->addField('title1', 'label', array(
          'after_element_html' => '<br /><br /><br />',
        ));
        
  	   $fieldset->addField('note_rule_program', 'label', array(
          'after_element_html' => '<div style="width: 500px; float:left;">Note: To create entire shopping cart rule go to Conditions tab.</div>',
        ));
		
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

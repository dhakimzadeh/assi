<?php
/**
 * Customer attribute add/edit form main tab
 *
 * @category    Zestard
 * @package     Zestard_Customerattribute
 * @author      Zestard Magento Team
 */

class  Zestard_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tab_Main extends Zestard_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Main_Main
{

    /**
     * Preparing default form elements for editing attribute
     *
     * @return Zestard_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('zestard_customerattribute')->__('Frontend Properties')));
        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('zestard_customerattribute')->__('Sort Order'),
            'title' => Mage::helper('zestard_customerattribute')->__('Sort Order'),
            'note' => Mage::helper('zestard_customerattribute')->__('The order to display attribute on the frontend'),
            'class' => 'validate-digits',
        ));

        $usedInForms = $attributeObject->getUsedInForms();

        $fieldset->addField('customer_account_create', 'checkbox', array(
            'name' => 'customer_account_create',
            'checked'   => in_array('customer_account_create', $usedInForms) ? true : false,
            'value'     => '1',
            'label' => Mage::helper('zestard_customerattribute')->__('Show on the Customer Account Create Page'),
            'title' => Mage::helper('zestard_customerattribute')->__('Show on the Customer Account Create Page'),
        ));

        $fieldset->addField('customer_account_edit', 'checkbox', array(
            'name' => 'customer_account_edit',
            'checked'   => in_array('customer_account_edit', $usedInForms) ? true : false,
            'value'     => '1',
            'label' => Mage::helper('zestard_customerattribute')->__('Show on the Customer Account Edit Page'),
            'title' => Mage::helper('zestard_customerattribute')->__('Show on the Customer Account Edit Page'),
        ));

        $fieldset->addField('adminhtml_customer', 'checkbox', array(
            'name' => 'adminhtml_customer',
            'checked'   => in_array('adminhtml_customer', $usedInForms) ? true : false,
            'value'     => '1',
            'label' => Mage::helper('zestard_customerattribute')->__('Show on the Admin Manage Customers'),
            'title' => Mage::helper('zestard_customerattribute')->__('Show on the Admin Manage Customers'),
            'note' => Mage::helper('zestard_customerattribute')->__('Show on the Admin Manage Customers Add and Edit customer Page'),
        ));

        $fieldset->addField('checkout_register', 'checkbox', array(
            'name' => 'checkout_register',
            'checked'   => in_array('checkout_register', $usedInForms) ? true : false,
            'value'     => '1',
            'label' => Mage::helper('zestard_customerattribute')->__('Show on the Checkout Billing'),
            'title' => Mage::helper('zestard_customerattribute')->__('Show on the Checkout Billing'),
            'note' => Mage::helper('zestard_customerattribute')->__('Show on the Checkout Billing'),
        ));

        return $this;
    }
}

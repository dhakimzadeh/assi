<?php
/**
 * Customer attribute edit page tabs
 *
 * @category    Zestard
 * @package     Zestard_Customerattribute
 * @author      Zestard Magento Team
 */
class Zestard_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('customerattribute_tabs');
      $this->setDestElementId('edit_form'); // this should be same as the form id define above
      $this->setTitle(Mage::helper('zestard_customerattribute')->__('Attribute Information'));
  }

 /**
  * Specified customer attribute edit page tabs
  */
  protected function _beforeToHtml()
  {
      $this->addTab('main', array(
            'label'     => Mage::helper('zestard_customerattribute')->__('Properties'),
            'title'     => Mage::helper('zestard_customerattribute')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('zestard_customerattribute/adminhtml_customerattribute_edit_tab_main')->toHtml(),
            'active'    => true
        ));

      $model = Mage::registry('customerattribute_data');

        $this->addTab('labels', array(
            'label'     => Mage::helper('zestard_customerattribute')->__('Manage Label / Options'),
            'title'     => Mage::helper('zestard_customerattribute')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('zestard_customerattribute/adminhtml_customerattribute_edit_tab_options')->toHtml(),
        ));

      return parent::_beforeToHtml();
  }
}
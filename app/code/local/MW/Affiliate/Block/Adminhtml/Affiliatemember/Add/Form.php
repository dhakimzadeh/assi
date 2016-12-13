<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
  	{
    	$form_member_detail = new Varien_Data_Form(array(
                                      'id' 		=> 'add_form',
                                      'action' 	=> $this->getUrl('adminhtml/affiliate_affiliatemember/savenew'),
                                      'method' 	=> 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form_member_detail->setUseContainer(true);
      $this->setForm($form_member_detail);
      return parent::_prepareForm();
  }
}
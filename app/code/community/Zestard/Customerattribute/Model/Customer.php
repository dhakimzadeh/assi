<?php
class Zestard_Customerattribute_Model_Customer extends Mage_Customer_Model_Customer
{
	/**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $this->_sendEmailTemplateCustom($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('customer' => $this, 'back_url' => $backUrl), $storeId);

        return $this;
    }

	/**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @return Mage_Customer_Model_Customer
     */
    protected function _sendEmailTemplateCustom($template, $sender, $templateParams = array(), $storeId = null)
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */        
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getEmail(), $this->getName());
        $mailer->addEmailInfo($emailInfo);

        //---------- code for add template variables in email -----------

        $add_in_customer_registration = Mage::getStoreConfig('zestard_customerattribute/customerattribute_group/add_in_customer_registration');

        $customerAddressAttribute = array();
		$Addressattributes = Mage::getModel('zestard_customerattribute/customerattribute')->getCollection();
		
		foreach ($Addressattributes->getData() as $key => $value) {
			$customerAddressAttribute[] = $value['attribute_code'];
		}

		$data = array();
		$postObject = new Zestard_Customerattribute_Block_Data();
        		
        if($add_in_customer_registration == 1) {		
            foreach($this->getData() as $key => $value) {
    			if(in_array($key,$customerAddressAttribute)) {					
    								
    				$attributeCollection = Mage::getModel('zestard_customerattribute/customerattribute')->getCollection()
    					->addFieldToFilter('attribute_code',$key);				
    				$attr = $attributeCollection->getData();																
    				if($attr[0]['send_register_email'] == 1 && $attr[0]['is_user_defined'] == 1) {				
    					$label = $attr[0]['frontend_label'];
    					$data[$label] = $value;					
    				}						
    			}
    		}
        }        
		$postObject->setData($data);
       
        // -------------code end -------------------------------
        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams(array_merge(array('CustomerAttribute' => $postObject),$templateParams));
        //$mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }
}
?>
<?php

class Sm_Instagramgallery_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('root')->setTemplate('sm/instagramgallery/imginstagram.phtml');
		$this->renderLayout();
	}

	public function viewlistAction()
	{
		$id = $this->getRequest()->getParam('id');
		$this->loadLayout();
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$block = $this->getLayout()->createBlock('instagramgallery/instagramusers_viewlist', '', array(
			'id'    => $id
		));
		$this->getLayout()->getBlock('content')->append($block);
		$this->_title(Mage::helper('instagramgallery')->__('SM Instagram Gallery'))
			->_title(Mage::helper('instagramgallery')->__('View List Instagram Users'));
		$this->renderLayout();
	}
}
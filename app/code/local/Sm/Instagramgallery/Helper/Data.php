<?php

class Sm_Instagramgallery_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_ENABLE_INSTAGRAMGALLERY   = 'instagramgallery/general/enabled';
	const XML_TITLE_INSTAGRAMGALLERY    = 'instagramgallery/general/title';
	const XML_USERS_INSTAGRAM       = 'instagramgallery/general/users';
	const XML_ACCESS_TOKEN          = 'instagramgallery/general/access_token';
	const XML_LIMIT_IMAGE           = 'instagramgallery/general/limit_image';
	const XML_NUMBER_COLUMNS1        = 'instagramgallery/numbercols/nb_column1';
	const XML_NUMBER_COLUMNS2       = 'instagramgallery/numbercols/nb_column2';
	const XML_NUMBER_COLUMNS3       = 'instagramgallery/numbercols/nb_column3';
	const XML_NUMBER_COLUMNS4       = 'instagramgallery/numbercols/nb_column4';
	const XML_INCLUDE_JQUERY        = 'instagramgallery/jquery/include_jquery';

	protected $instagramgalleryInstance;

	public function getImgInstagramInstance()
	{
		if(!$this->instagramgalleryInstance)
		{
			$this->instagramgalleryInstance = Mage::registry('instagramusers');
			if(!$this->instagramgalleryInstance)
			{
				Mage::throwException($this->__('Instagram users instance does not exit in Registry'));
			}
		}
		return $this->instagramgalleryInstance;
	}

	public function enabledInstagramgallery($store = null)
	{
		return Mage::getStoreConfigFlag(self::XML_ENABLE_INSTAGRAMGALLERY, $store);
	}

	public function usersInstagram($store = null)
	{
		return Mage::getStoreConfig(self::XML_USERS_INSTAGRAM, $store);
	}

	public function titleInstagram($store = null)
	{
		return Mage::getStoreConfig(self::XML_TITLE_INSTAGRAMGALLERY, $store);
	}

	public function accessToken($store = null)
	{
		return Mage::getStoreConfig(self::XML_ACCESS_TOKEN, $store);
	}

	public function limitImage($store = null)
	{
		return Mage::getStoreConfig(self::XML_LIMIT_IMAGE, $store);
	}

	public function numcolsScreen1200($store = null)
	{
		return Mage::getStoreConfig(self::XML_NUMBER_COLUMNS1, $store);
	}

	public function numcolsScreen992($store = null)
	{
		return Mage::getStoreConfig(self::XML_NUMBER_COLUMNS2, $store);
	}

	public function numcolsScreen768($store = null)
	{
		return Mage::getStoreConfig(self::XML_NUMBER_COLUMNS3, $store);
	}

	public function numcolsScreenLesThan768($store = null)
	{
		return Mage::getStoreConfig(self::XML_NUMBER_COLUMNS4, $store);
	}

	public function getIncludeJquery($store = null)
	{
		return Mage::getStoreConfig(self::XML_INCLUDE_JQUERY, $store);
	}

	public function getInlucdeJQquery()
	{
		if (!(int)$this->enabledInstagramgallery()) return;
		if (!defined('MAGENTECH_JQUERY') && (int)$this->getIncludeJquery()) {
			define('MAGENTECH_JQUERY', 1);
			$_jquery_libary = 'sm/instagramgallery/js/jquery-2.1.3.min.js';
			return $_jquery_libary;
		}
	}

	public function getInlucdeNoconflict()
	{
		if (!(int)$this->enabledInstagramgallery()) return;
		if (!defined('MAGENTECH_JQUERY_NOCONFLICT') && (int)$this->getIncludeJquery()) {
			define('MAGENTECH_JQUERY_NOCONFLICT', 1);
			$_jquery_noconflict = 'sm/instagramgallery/js/jquery-noconflict.js';
			return $_jquery_noconflict;
		}
	}

	public function getInlucdeMigrate()
	{
		if (!(int)$this->enabledInstagramgallery()) return;
		if (!defined('MAGENTECH_JQUERY_MIGRATE') && (int)$this->getIncludeJquery()) {
			define('MAGENTECH_JQUERY_MIGRATE', 1);
			$_jquery_noconflict = 'sm/instagramgallery/js/jquery-migrate-1.2.1.min.js';
			return $_jquery_noconflict;
		}
	}
}
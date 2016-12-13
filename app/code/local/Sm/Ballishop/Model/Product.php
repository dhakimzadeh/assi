<?php
class Sm_Ballishop_Model_Product extends Mage_Catalog_Model_Abstract
{

    /**
     * Initialize resources
     */
    protected function _construct()
    {
        $this->_init('catalog/product');
    }

/*******************************************************************************/
    public function getMediaGalleryImages()
    {
        if(!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {
            $images = new Varien_Data_Collection();
			//$limit = 3;
			$limit = Mage::helper('ballishop/config')->getProductListing('slide_limit');
			$i=0;
            foreach ($this->getMediaGallery('images') as $image) {
				$i++;
                if ($image['disabled']) {
                    continue;
                }
                $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                $image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
                $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
                $images->addItem(new Varien_Object($image));
				
				if($i == $limit && $limit != 0) break;
            }
            $this->setData('media_gallery_images', $images);
        }
		
        return $this->getData('media_gallery_images');
		
		
    }

    /**
     * Retrive product media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    public function getMediaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    /**
     * Whether the product is a recurring payment
     *
     * @return bool
     */
    public function isRecurring()
    {
        return $this->getIsRecurring() == '1';
    }

}

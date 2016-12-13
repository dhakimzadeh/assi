<?php
/*------------------------------------------------------------------------
 # SM Ballishop - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Ballishop_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action {
	

    public function blocksAction() {
		$overwrite = Mage::getStoreConfig('ballishop_cfg/theme_install/overwrite_blocks');
        try {
			$filename = Mage::getModuleDir('etc', 'Sm_Ballishop') .'/import/blocks.xml';
            if (!is_readable($filename)) {
                throw new Exception(Mage::helper('ballishop')->__("Can't read data file: %s", $filename));
            }
            $xml = new Varien_Simplexml_Config($filename);
			
            $conflictingOldItems = array();
            $imported = 0;
            foreach ($xml->getNode('blocks')->children() as $node) {
                //Check if block already exists
                $oldBlocks = Mage::getModel('cms/block')->getCollection()
                    ->addFieldToFilter('identifier', $node->identifier)
                    ->load();
        
                //If items can be overwritten
                if (count($oldBlocks) > 0) {
                    $conflictingOldItems[] = $node->identifier;
                    if (!$overwrite) continue;
                    foreach ($oldBlocks as $old) {
                        $old->delete();
                    }
                }
                
                $node_store_ids = array(0);
                $node_store = (string)$node->store_id;
                if (!empty($node_store)){
                    $tmps = explode(",", $node_store);
                    foreach ($tmps as $str){
                        if ($sid = intval($str)){
                            $node_store_ids[] = $sid;
                        }
                    }
                }
                
                Mage::getModel('cms/block')
    				->setTitle($node->title)
    				->setContent($node->content)
    				->setIdentifier($node->identifier)
    				->setIsActive($node->is_active)
					->setStores($node_store_ids)
    				->save();
                $imported++;
        
            }
            	
            $imported
                ? Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ballishop')->__('Number of imported items: %s', $imported))
                : Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('ballishop')->__('No items were imported'));
            	
            if ($conflictingOldItems) {
                $overwrite
                    ? Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ballishop')->__('Items (%s) with the following identifiers were overwritten:<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems)))
                    : Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('ballishop')->__('Unable to import items (%s) with the following identifiers (they already exist in the database):<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems)));
            }
            	
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::logException($e);
        }
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/ballishop_cfg/"));

    }

    public function pagesAction() {
		$overwrite = Mage::getStoreConfig('ballishop_cfg/theme_install/overwrite_pages');
        try {
			$filename = Mage::getModuleDir('etc', 'Sm_Ballishop') .'/import/pages.xml';
			if (!is_readable($filename)) {
				throw new Exception(Mage::helper('ballishop')->__("Can't read data file: %s", $filename));
			}
			$xml = new Varien_Simplexml_Config($filename);
			
			$conflictingOldItems = array();
			$imported = 0;
			foreach ($xml->getNode('pages')->children() as $node) {
				//Check if block already exists
				$oldBlocks = Mage::getModel('cms/page')->getCollection()
					->addFieldToFilter('identifier', $node->identifier)
					->load();
				
				//If items can be overwritten
				if (count($oldBlocks) > 0) {
				    $conflictingOldItems[] = $node->identifier;
				    if (!$overwrite) continue;
				    foreach ($oldBlocks as $old) {
				        $old->delete();
				    }
				}
				
				$node_store_ids = array(0);
				$node_store = (string)$node->store_id;
				if (!empty($node_store)){
				    $tmps = explode(",", $node_store);
				    foreach ($tmps as $str){
				        if ($sid = intval($str)){
				            $node_store_ids[] = $sid;
				        }
				    }
				}
				
				Mage::getModel('cms/page')
					->setTitle($node->title)
					->setRootTemplate($node->root_template)
					->setContentHeading($node->content_heading)
					->setContent($node->content)
					->setLayoutUpdateXml($node->layout_update_xml)
					->setIdentifier($node->identifier)
					->setIsActive($node->is_active)
					->setStores($node_store_ids)
					->save();
			    $imported++;
				
			}
			
			$imported
			    ? Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ballishop')->__('Number of imported items: %s', $imported))
			    : Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('ballishop')->__('No items were imported'));
			
			if ($conflictingOldItems) {
			    $overwrite
			        ? Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ballishop')->__('Items (%s) with the following identifiers were overwritten:<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems)))
			        : Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('ballishop')->__('Unable to import items (%s) with the following identifiers (they already exist in the database):<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems)));
			}
			
		} catch (Exception $e) {
			
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::logException($e);
		}

        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/ballishop_cfg/"));
    }
	
	public function menuAction() {

		$menugroups = Mage::getModel("megamenu/menugroup")->getCollection();
		$menuitems = Mage::getModel("megamenu/menuitems")->getCollection();
		
		if($menuitems){
			foreach ($menuitems as $menuitem) {
				$menuitem->delete();
			}
		}

		if($menugroups){
			foreach ($menugroups as $menugroup) {
				$menugroup->delete();
			}
		}

		$installer = new Mage_Core_Model_Resource_Setup('core_setup');
		$installer->startSetup();
		
		$file = Mage::getModuleDir('etc', 'Sm_Ballishop') .'\import\megamenu.sql';
		if (is_file($file) && ($sqls = file_get_contents ($file))) {
			$installer->run ($sqls);
			$installer->endSetup();

			$this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/ballishop_cfg/"));
			$this->_getSession()->addSuccess( Mage::helper('megamenu')->__('Importing the sample data successfully.') );
		} else{
			$this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/ballishop_cfg/"));
			$this->_getSession()->addError( Mage::helper('megamenu')->__('Importing error.') );
		}

	}
}

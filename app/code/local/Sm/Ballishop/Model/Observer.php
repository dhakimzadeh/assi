<?php
/*------------------------------------------------------------------------
 # SM ballishop - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
//die('aaa');
class Sm_Ballishop_Model_Observer {

	public function less_compile( $observer ){	
	
		$setting = Mage::helper('ballishop/data');
		$less_theme_compile     = Mage::getStoreConfig('ballishop_cfg/advanced/less_compile');
		
		if (isset($_GET['color'])) {
			$preset_name = $_GET['color'];
			setcookie( "color", $preset_name);
		} else{
			if(!isset($_COOKIE['color'])){
				$preset_name = Mage::getStoreConfig('ballishop_cfg/general/theme_color');
			} else{
				$preset_name = $_COOKIE['color'];
			}
			
		}
		
		//$preset_name 			= Mage::getStoreConfig('ballishop_cfg/general/theme_color');
		$device_responsive      = Mage::getStoreConfig('ballishop_cfg/theme_layout/device_responsive');
		//$direction      		= Mage::getStoreConfig('ballishop_cfg/theme_layout/direction');
		
		if ( !Mage::app()->getStore()->isAdmin() && $less_theme_compile ){
			if (!class_exists('Less_Parser')) {			
				include_once(Mage::getBaseDir('lib').'Less/Version.php');
				include_once(Mage::getBaseDir('lib').'Less/Parser.php');
			}

			if ( class_exists('Less_Parser') && $less_theme_compile ){
				$skin_base_dir = Mage::getDesign()->getSkinBaseDir();
				$skin_base_url = Mage::getDesign()->getSkinUrl();

				define('LESS_PATH', $skin_base_dir.'/less');
				define('CSS__PATH', $skin_base_dir.'/css');						
				
				$import_dirs = array(
						LESS_PATH.'/path/' => $skin_base_url.'/less/path/',
						LESS_PATH.'/bootstrap/' => $skin_base_url.'/less/bootstrap/'
				);
				$options = array( 'compress'=>true );
				
				if ( file_exists(LESS_PATH.'/theme.less') && $less_theme_compile  ){
				
					if ( $preset_name ){
						$output_cssf = CSS__PATH.'/theme-'.$preset_name.'.css';
					} else {
						$output_cssf = CSS__PATH.'/theme-default.css';
					}
					
					$less = new Less_Parser($options);
					$less->SetImportDirs( $import_dirs );
					$less->parseFile(LESS_PATH.'/theme.less', $skin_base_url.'css/');
					
					if ( file_exists(LESS_PATH.'/theme-'.$preset_name.'.less') ){
						$less->parseFile(LESS_PATH.'/theme-'.$preset_name.'.less', $skin_base_url.'css/');
					}
					
					//get cpanel frontend
					$cPanel = Mage::helper('ballishop/config')->getAdvanced('show_cpanel');
					if($cPanel){
						$less->parseFile(LESS_PATH.'/path/new_cpanel.less', $skin_base_url.'css/');
					}

					//get newsletter popup
					$popupNewsletter = Mage::helper('ballishop/config')->getAdvanced('show_newsletter');
					if($popupNewsletter){
						$less->parseFile(LESS_PATH.'/path/newsletter-popup.less', $skin_base_url.'css/');
					}
					
					//get fancybox style
					
					$lightBoxTypeCss = Mage::helper('ballishop/config')->getProductSetting('lightbox_types');
					if ($lightBoxTypeCss == "thumbs") {
						$less->parseFile(LESS_PATH.'/path/jquery.fancybox-thumbs.less', $skin_base_url.'css/');
					} else if ($lightBoxTypeCss == "button") {
						$less->parseFile(LESS_PATH.'/path/jquery.fancybox-buttons.less', $skin_base_url.'css/');
					}
					
					//get header general style
					$less->parseFile(LESS_PATH.'/path/header/header-general.less', $skin_base_url.'css/');
					
					//get footer general style
					$less->parseFile(LESS_PATH.'/path/footer/footer-general.less', $skin_base_url.'css/');
					
					//get header style
					/*if (isset($_GET['header'])) {
						$headerStyle = $_GET['header'];
						setcookie( "header", $headerStyle);
					} else{
						if($_COOKIE['header']==null){
							$headerStyle = Mage::helper('ballishop/config')->getThemeLayout('header_style');
						} else{
							$headerStyle = $_COOKIE['header'];
						}
						
					}	*/				
					$headerStyle = Mage::helper('ballishop/config')->getThemeLayout('header_style');
					$less->parseFile(LESS_PATH.'/path/header/header-style-' .$headerStyle . '/header-style.less', $skin_base_url.'css/');
					
					//get home page style
					$homeStyle = Mage::helper('ballishop/config')->getThemeLayout('cmspage_style');
					$less->parseFile(LESS_PATH.'/path/homepage/home-style-' .$homeStyle . '/home-style.less', $skin_base_url.'css/');
					
					/*get footer style*/
					$footerStyle = Mage::helper('ballishop/config')->getThemeLayout('footer_style');
					$less->parseFile(LESS_PATH.'/path/footer/footer-style-' .$footerStyle . '/footer-style.less', $skin_base_url.'css/');

					if( $device_responsive == 1 ){
						//get header responsive style
						$less->parseFile(LESS_PATH.'/path/header/header-style-' . $headerStyle . '/responsive-header.less', $skin_base_url.'css/');
						
						//get footer responsive style
						$less->parseFile(LESS_PATH.'/path/footer/footer-style-' . $footerStyle . '/responsive-footer.less', $skin_base_url.'css/');

						//get home responsive style
						$less->parseFile(LESS_PATH.'/path/homepage/home-style-' .$homeStyle . '/responsive-home.less', $skin_base_url.'css/');
						
						//get responsive listing page
						$less->parseFile(LESS_PATH.'/path/listing-page-responsive.less', $skin_base_url.'css/');
						
						//get responsive detail page
						$less->parseFile(LESS_PATH.'/path/detail-page-responsive.less', $skin_base_url.'css/');

						//get responsive theme
						$less->parseFile(LESS_PATH.'/path/yt-responsive.less', $skin_base_url.'css/');
					} else {
						$less->parseFile(LESS_PATH.'/path/yt-non-responsive.less', $skin_base_url.'css/');
					}

					/* if( $direction == 2 ){
						//get header right to left style
						$less->parseFile(LESS_PATH.'/path/header/header-style-' . $headerStyle . '/header-style-rtl.less', $skin_base_url.'css/');
						
						//get footer right to left style
						$less->parseFile(LESS_PATH.'/path/footer/footer-style-' . $footerStyle . '/footer-style-rtl.less', $skin_base_url.'css/');
						
						//get theme right to left
						$less->parseFile(LESS_PATH.'/path/theme-rtl.less', $skin_base_url.'css/');

						if( $device_responsive == 1 ){
							//get header right to left responsive style
							$less->parseFile(LESS_PATH.'/path/header/header-style-' . $headerStyle . '/responsive-header-rtl.less', $skin_base_url.'css/');
							
							//get footer right to left responsive style
							$less->parseFile(LESS_PATH.'/path/footer/footer-style-' . $footerStyle . '/responsive-footer-rtl.less', $skin_base_url.'css/');
						
							$less->parseFile(LESS_PATH.'/path/yt-responsive-rtl.less', $skin_base_url.'css/');
						}
					} */
					
					$cache = $less->getCss();
					file_put_contents($output_cssf, $cache);
					
				}
			
			}
		}
		
	}			
	
}
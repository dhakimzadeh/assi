<?php
/*------------------------------------------------------------------------
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

//params config theme
$_params = new ThemeParameter();
$_config = array(	
	'layout_styles'					=>'1',
	'menu_styles'					=>'1',
);
$attributes = array();
if( Mage::getConfig()->getNode('modules/Sm_Ballishop') ){
	$_config = Mage::helper('ballishop/data')->get($attributes);
}
// Layout
if($_config['layout_styles'] == 1) { $layout_style='1';}	
if($_config['layout_styles'] == 2) { $layout_style='2';}	
$_params->set('layoutstyle',$layout_style);

// Menu Type
if($_config['menu_styles'] ==1) { $menu_style='mega';}	
if($_config['menu_styles'] ==2) { $menu_style='css';}	
$_params->set('menustyle',$menu_style);

// Array param for cookie
$paramscookie = array(
				  'layoutstyle',
				  'menustyle'
);
global $var_yttheme;
$var_yttheme = new YtTheme('sm_ballishop', $_params, $paramscookie);

<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 28-06-2015
 * Time: 14:35
 */
class Sm_Megamenu_Block_Adminhtml_Widget_AddField extends Mage_Core_Block_Template
{
	protected	$_p ;
	protected	$_b ;

	public function __construct(){
		$this->_p = new Varien_Object();
		$this->_b = new Varien_Object();
	}
	public function addFieldWidget($arr, $fieldset){
		$param = $this->_p;
		$button = $this->_b;
		$param->setKey(($arr['id'])?$arr['id']:'empty_id');
//		if ($arr['visible'])
//			$param->setVisible($arr['visible']);
//		else
		$param->setVisible(1);
//		if ($arr['required'])
//			$param->setRequired($arr['required']);
//		else
		$param->setRequired(1);
//		if ($arr['type'])
//			$param->setType($arr['type']);
//		else
		$param->setType('Label');

		$param->setSortOrder(($arr['sort_order'])?$arr['sort_order']:1);
//		if ($arr['values'])
//			$param->setValues($arr['values']);
//		else
		$param->setValues(array());

		$param->setLabel(($arr['label'])?$arr['label']:'Empty');

		$button->setButton(($arr['button']['text'])?$arr['button']['text']:array('open'=>'Select...'));
		$button->setType(($arr['button']['type'])?$arr['button']['type']:'');
		$param->setHelperBlock($button);
		return $this->_addField($param,$fieldset);
	}
	public function getMainFieldset($fieldset)
	{
		if ($this->_getData('main_fieldset') instanceof Varien_Data_Form_Element_Fieldset) {
			return $this->_getData('main_fieldset');
		}
		$this->setData('main_fieldset', $fieldset);
		return $fieldset;
	}
	public function _addField($parameter,$fieldset)
	{
		$form = $this->getForm();
		$fieldset = $this->getMainFieldset($fieldset); //$form->getElement('options_fieldset');

		// prepare element data with values (either from request of from default values)
		$fieldName = $parameter->getKey();
		$data = array(
			//'name'      => $form->addSuffixToName($fieldName, 'parameters'),
			'name'      => $fieldName,
			'label'     => Mage::helper('megamenu')->__($parameter->getLabel()),
			// 'required'  => $parameter->getRequired(),
			'class'     => 'widget-option '.$fieldName,
			'note'      => Mage::helper('megamenu')->__($parameter->getDescription()),
		);

		if ($values = $this->getWidgetValues()) {
			$data['value'] = (isset($values[$fieldName]) ? $values[$fieldName] : '');
		}
		else {
			$data['value'] = $parameter->getValue();
			//prepare unique id value
			if ($fieldName == 'unique_id' && $data['value'] == '') {
				// $data['value'] = md5(microtime(1));
				$data['value'] = microtime(1);
			}
		}

		// prepare element dropdown values
		if ($values  = $parameter->getValues()) {
			// dropdown options are specified in configuration
			$data['values'] = array();
			foreach ($values as $option) {
				$data['values'][] = array(
					'label' => Mage::helper('megamenu')->__($option['label']),
					'value' => $option['value']
				);
			}
		}
		// otherwise, a source model is specified
		elseif ($sourceModel = $parameter->getSourceModel()) {
			$data['values'] = Mage::getModel($sourceModel)->toOptionArray();
		}

		$fieldRenderer = null;
		$fieldType = $parameter->getType();
		// hidden element
		if (!$parameter->getVisible()) {
			$fieldType = 'hidden';
		}
		// just an element renderer
		elseif (false !== strpos($fieldType, '/')) {
			$fieldRenderer = $this->getLayout()->createBlock($fieldType);
			$fieldType = $this->_defaultElementType;
		}

		// instantiate field and render html
		// $field = $fieldset->addField($this->getMainFieldsetHtmlId() . '_' . $fieldName, $fieldType, $data);
		$field = $fieldset->addField($fieldName, $fieldType, $data);
		if ($fieldRenderer) {
			$field->setRenderer($fieldRenderer);
		}

		// extra html preparations
		if ($helper = $parameter->getHelperBlock()) {
			Mage::register('megamenu_adminhtml_widget_chooser',1);	// cho phep block Sm_Megamenu_Block_Adminhtml_Widget_Chooser check widget available for megamenu
			$helperBlock = $this->getLayout()->createBlock($helper->getType(), '', $helper->getData());
			if ($helperBlock instanceof Varien_Object) {
				$helperBlock->setConfig($helper->getData())
					->setFieldsetId($fieldset->getId())
					->setTranslationHelper(Mage::helper('megamenu'))
					->prepareElementHtml($field);
			}
		}

		return $field;
	}

	protected function _getButtonHtml($data)
	{
		$html = '<button type="button"';
		$html.= ' class="scalable '.(isset($data['class']) ? $data['class'] : '').'"';
		$html.= isset($data['onclick']) ? ' onclick="'.$data['onclick'].'"' : '';
		$html.= isset($data['style']) ? ' style="'.$data['style'].'"' : '';
		$html.= isset($data['id']) ? ' id="'.$data['id'].'"' : '';
		$html.= '>';
		$html.= isset($data['title']) ? '<span>'.$data['title'].'</span>' : '';
		$html.= '</button>';

		return $html;
	}

}
<?php

class Sm_Instagramgallery_Model_Source_Numcols
{
	public function toOptionArray()
	{
		return array(
			array(
				'value'     => 1,
				'label'     => Mage::helper('instagramgallery')->__('1'),
			),
			array(
				'value'     => 2,
				'label'     => Mage::helper('instagramgallery')->__('2'),
			),
			array(
				'value'     => 3,
				'label'     => Mage::helper('instagramgallery')->__('3'),
			),
			array(
				'value'     => 4,
				'label'     => Mage::helper('instagramgallery')->__('4'),
			),
			array(
				'value'     => 5,
				'label'     => Mage::helper('instagramgallery')->__('5'),
			),
			array(
				'value'     => 6,
				'label'     => Mage::helper('instagramgallery')->__('6'),
			)
		);
	}
}
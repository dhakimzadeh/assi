<?php
class Shipwire_Shipping_Model_Carrier_Service {
    public function toOptionArray()
    {
		return array(
			array(
				'value' => '1D',
				'label' => 'One Day Service (1D)'
			),
			array(
				'value' => '2D',
				'label' => 'Two Day Service (2D)'
			),
			array(
				'value' => 'GD',
				'label' => 'Ground Service (GD)'
			),
			array(
				'value' => 'FT',
				'label' => 'Freight Service (FT)'
			),
			array(
				'value' => 'INTL',
				'label' => 'International Service (INTL)'
			),
			array(
				'value' => 'E-INTL',
				'label' => 'International Economy Service (E-INTL)'
			),
			array(
				'value' => 'PL-INTL',
				'label' => 'International plus Service (PL-INTL)'
			),
			array(
				'value' => 'PM-INTL',
				'label' => 'International Premium Service (PM-INTL)'
			)
		);
    }
}
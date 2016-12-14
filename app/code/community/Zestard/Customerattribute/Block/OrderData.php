<?php
class Zestard_Customerattribute_Block_OrderData extends Varien_Object {
    public function arrayToHtml() {
        if($this->getData() != NULL)
	    {    
	        $result = '<h6>Information</h6>';
	        foreach ($this->getData() as $key => $value) {
	            $result .= '<span>'.ucfirst($key) . ' : ' . ((trim($value) != "") ? $value : 'N/A') . '</span><br />';
	        }
	    }
        return $result;
    }
}
?>

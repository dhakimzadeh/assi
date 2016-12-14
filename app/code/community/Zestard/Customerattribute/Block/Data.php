<?php
class Zestard_Customerattribute_Block_Data extends Varien_Object {
    public function arrayToHtml() {
        $result = '<br /><b>Information :</b> <br />';
        foreach ($this->getData() as $key => $value) {
            $result .= '<b>'.ucfirst($key) . '</b> : ' . ((trim($value) != "") ? $value : 'N/A') . '<br />';
        }
        return $result;
    }
}
?>

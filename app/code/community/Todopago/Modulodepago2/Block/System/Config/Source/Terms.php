<?php
class Todopago_Modulodepago2_Block_System_Config_Source_Terms extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml()
    {
        $html = "<input type='checkbox' class='required-entry' name='terms' value='1'> Terms and Conditions (<a href='#'>Click Here</a>)";
        return $html;
    }
}

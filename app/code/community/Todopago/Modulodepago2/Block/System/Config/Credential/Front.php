<?php

class Todopago_Modulodepago2_Block_System_Config_Credential_Front extends Mage_Adminhtml_Block_System_Config_Form_Field
{
     /**
     * @var string
     */
    protected $_wizardTemplate = 'modulodepago2/system/config/credential.phtml';

    /**
     * Set template to itself
     */
    protected $_mydata = array();

    public function getButtonLabel()
    {
        return $this->_mydata['button_label'];
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate($this->_wizardTemplate);
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $elementHtmlId = $element->getHtmlId();
        $this->addData(array_merge(
            $this->_getButtonData($elementHtmlId, $originalData),
            $this->_getSandboxButtonData($elementHtmlId, $originalData)
        ));
        $this->_mydata = array_merge(
            $this->_getButtonData($elementHtmlId, $originalData),
            $this->_getSandboxButtonData($elementHtmlId, $originalData)
        );
        
        return $this->_toHtml();
    }

    /**
     * Prepare button data
     *
     * @param string $elementHtmlId
     * @param array $originalData
     * @return array
     */
    protected function _getButtonData($elementHtmlId, $originalData)
    {
        return array(
            'button_label' => $originalData['button_label'],
            'button_url'   => Mage::helper("adminhtml")->getUrl($originalData['button_url']),
            'html_id' => $elementHtmlId,
        );
    }

    /**
     * Prepare sandbox button data
     *
     * @param string $elementHtmlId
     * @param array $originalData
     * @return array
     */
    protected function _getSandboxButtonData($elementHtmlId, $originalData)
    {
        return array(
            'sandbox_button_label' => $originalData['sandbox_button_label'],
            'sandbox_button_url'   => Mage::helper("adminhtml")->getUrl($originalData['sandbox_button_url']),
            'sandbox_html_id' => 'sandbox_' . $elementHtmlId,
        );
    }
}
<?php
class Todopago_Modulodepago2_Block_System_Config_Source_Banners extends Mage_Adminhtml_Block_System_Config_Form_Field
{	

	protected $_values = null;

    protected function _construct()
    {   
        $this->setTemplate('modulodepago2/system/config/banners.phtml');
        return parent::_construct();
    }
    
    
    public function getValue() {
        $value = Mage::getStoreConfig('payment/modulodepago2/banners');
        
        if($value == ""){
            Mage::getModel('core/config')->saveConfig('payment/modulodepago2/banners', "1");
            Mage::getModel('core/config')->cleanCache();
        }

        return $value;
    }
    
    public function getList(){

        $banner = array(
                        1 => "https://todopago.com.ar/sites/todopago.com.ar/files/billetera/pluginstarjeta1.jpg",
                        2 => "https://todopago.com.ar/sites/todopago.com.ar/files/billetera/pluginstarjeta2.jpg",
                        3 => "https://todopago.com.ar/sites/todopago.com.ar/files/billetera/pluginstarjeta3.jpg"
                    );

        return $banner;
    }

    public function getBanner(){
        $saved_banner = $this->getValue();
        $banners_list = $this->getList();

        if (array_key_exists($saved_banner, $banners_list)) {
            return $banners_list[$saved_banner];
        }
        
        return $banners_list[1];
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }
}


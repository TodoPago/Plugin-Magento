<?php
$installer = $this;
$installer->startSetup();

$sql=<<<SQLTEXT
DROP TABLE IF EXISTS {$this->getTable('todopagotable')};
CREATE TABLE {$this->getTable('todopagotable')} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` VARCHAR(255) NULL DEFAULT NULL,
  `request_key` VARCHAR(255) NULL DEFAULT NULL,
  `answer_key` VARCHAR(255) NULL DEFAULT NULL,
  `sendauthorizeanswer_status` VARCHAR(255) NULL DEFAULT NULL,
  `getauthorizeanswer_status` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);
SQLTEXT;


$installer->run($sql);

$sql =<<<SQLTEXT
DROP TABLE IF EXISTS {$this->getTable('todopagoaddress')};
CREATE TABLE {$this->getTable('todopagoaddress')} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `address` VARCHAR(255) NULL DEFAULT NULL,
  `city` VARCHAR(255) NULL DEFAULT NULL,
  `postal_code` VARCHAR(255) NULL DEFAULT NULL,
  `country` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);
SQLTEXT;

$installer->run($sql);


$installer->addAttribute("order", "todopagoclave", array("type"=>"varchar"));
$installer->addAttribute("quote_address", "todopago_costofinanciero", array("type"=>"varchar"));
$installer->addAttribute("order", "todopago_costofinanciero", array("type"=>"varchar"));


$sql=<<<SQLTEXT
UPDATE {$this->getTable('core_config_data')} SET value = 'Todo Pago' WHERE path = 'payment/modulodepago2/title';
SQLTEXT;

$installer->run($sql);

/** COMENTO  CREACION DE ATRIBUTO CELULAR**/
/*
$installer->addAttribute("customer", "celular",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Celular",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

    ));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "celular");
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="checkout_register";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
$used_in_forms[]="adminhtml_checkout";
        $attribute->setData("used_in_forms", $used_in_forms)
        ->setData("is_used_for_customer_segment", true)
        ->setData("is_system", 0)
        ->setData("is_user_defined", 1)
        ->setData("is_visible", 1)
        ->setData("sort_order", 100)
        ;
        $attribute->save();

*/   
$installer->endSetup();
     
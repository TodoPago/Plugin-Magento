Fork "abierto" de TodoPago para Magento
============

Mantengo este fork dado que el repositorio oficial no acepta Pull Requests de la comunidad.
En cuanto esa situación se remedie abandonaré el mantenimiento del mismo.

## Disclaimer
Este módulo no es de mi autoría y, por lo tanto, no me responsabilizo en ninguna medida por su funcionamiento o por cualquier incidente que su uso pudiera ocasionar sobre los sistemas en los que fuera instalado.
No soy empleado, asociado ni estoy en ninguna otra manera relacionado con TodoPago, Prisma, Visa o cualquier otra empresa o entidad financiera que pudiera relacionarse con las marcas y servicios que puedan asociarse al uso de este módulo tanto individualmente como en nombre de toda sociedad de la que yo forme parte.

## Contribuciones incorporadas

Antes de considerar el uso de este fork por sobre el desarrollo oficial se recomienda un análisis profundo de las modificaciones existentes en el mismo.

A continuación un listado de todas las modificaciones que fueron incorporadas para su consideración. Esta lista podría no estar completamente sincronizada con todas las contribuciones que fueran aceptadas aunque prometo hacer mi mayor esfuerzo para intentar mantenerla lo más al día posible.

- Incorporación de composer.json y modman para manejo de dependencias - [barbazul](https://github.com/barbazul)
 - [6a6a972](https://github.com/barbazul/Plugin-Magento/commit/6a6a972dc9720dc5e932376fecada215cd6ca9b1)
- Agregado el nombre del producto en la descripción que se envía al gateway - [dsueiro](https://github.com/dsueiro)
 - [9dd6b5f](https://github.com/barbazul/Plugin-Magento/commit/9dd6b5fa91cca74aea01aed78ad4d57defd963e6)
- Eliminado un rewrite sobre el bloque de agreements que rompía el funcionamiento de los agreements de magento - [augustojbusso](https://github.com/augustojbusso)
 - [53f1992](https://github.com/barbazul/Plugin-Magento/commit/53f1992bcc579da7b4c9c69081648a36440aa247)
 - [PR-1](https://github.com/barbazul/Plugin-Magento/pull/1)
- Fix del valor default faltante en la definicion de Todopago_Modulodepago2_Helper_Connector::getModo - [barbazul](https://github.com/barbazul)
 - [33049f4](https://github.com/barbazul/Plugin-Magento/commit/33049f42cb16677d3cf8af14c0ecb5120ebf168b)
- Corregido un error de sintaxis en el SDK - [barbazul](https://github.com/barbazul)
 - [7d51680](https://github.com/barbazul/Plugin-Magento/commit/7d516802c518f21fd75a1a6e7510f89c49902ef5)
- Inicializada una variable en el controller Payment que interrumpia el checkout en modo desarrollador - [barbazul](https://github.com/barbazul)
 - [b095ed7](https://github.com/barbazul/Plugin-Magento/commit/b095ed793b76ac64ab20346bf5afff02d071bfc5)
- Fix para que el formulario hibrido funcione por ajax - [barbazul](https://github.com/barbazul)
 - [29b1980](https://github.com/barbazul/Plugin-Magento/commit/29b19805877331ecad8a916cfd0292d0e96c303f)


<a name="inicio"></a>
Magento- módulo Todo Pago (v1.7.x a 1.9.x)
============

Plug in para la integración con gateway de pago <strong>Todo Pago</strong>
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalación](#instalacion)
- [Configuración](#configuracion)
 - [Configuración plug in](#confplugin)
 - [Formulario Hibrido](#formHibrido)
 - [Obtener datos de configuracion](#getcredentials)
 - [Configuración de Maximo de Cuotas](#maxcuotas)
 - [Nuevas columnas y atributos](#tca)
- [Prevencion de Fraude](#cybersource)
 - [Consideraciones generales](#cons_generales)
 - [Consideraciones para vertical retail](#cons_retail)
 - [Datos adiccionales para prevención de fraude](#prevfraudedatosadicionales) 
- [Características](#features) 
 - [Consulta de transacciones](#constrans)
 - [Devoluciones](#devoluciones)
- [Tablas de referencia](#tablas)
- [Versiones disponibles](#availableversions)

<a name="consideracionesgenerales"></a>
## Consideraciones Generales
El plug in de pagos de <strong>Todo Pago</strong>, provee a las tiendas Magento de un nuevo m&eacute;todo de pago, integrando la tienda al gateway de pago.
La versión de este plug in esta testeada en PHP 5.3 en adelante y MAGENTO 1.7 a 1.9

<a name="instalacion"></a>
## Instalación
1. Descomprimir el archivo magento-plugin-master.zip. 
2.	Copiar carpeta 'app', 'js', 'skin' y 'lib' al root de magento con los mismos nombres.
3.	Ir a  System->Cache Managment y refrescar el cache.
4.	Luego ir a 'System->Configuration , luego en el menu lateral sales->Payment Methods' y configurar desde la pestaña de <strong>Todo Pago</strong>.

Observaci&oacute;n:
Descomentar: <em>extension=php_curl.dll</em>, <em>extension=php_soap.dll</em> y <em>extension=php_openssl.dll</em> del php.ini, ya que para la conexión al gateway se utiliza la clase <em>SoapClient</em> del API de PHP.
<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="configuracion"></a>
##Configuración

<a name="confplugin"></a>
####Configuración plug in
Para llegar al menu de configuración ir a: <em>System->Configuration</em> y seleccionar Payment Methods en el menú izquierdo. Entre los medios de pago aparecerá una solapa con el nombre <strong>Todo Pago</strong>. El Plug-in esta separado en configuarción general y 3 sub-menues.
<sub></br><em>Menú principal</em></br></sub>
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/magento_1.PNG)
<sub></br><em>Menú ambiente</em></br></sub>
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/magento_ambientes.PNG) 
<sub></br><em>Meenú estados y menú servicios</em></br></sub>
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/magento_configEstados.PNG) 
</br>
[<sub>Volver a inicio</sub>](#inicio)

<a name="formHibrido"></a>
####Formulario Hibrido
En la configuracion del plugin tambien estara la posibilidad de mostrarle al cliente el formulario de pago de TodoPago integrada en el sitio. 
Para esto , en la configuracion se debe seleccionar YES en el campo Utilizar formulario híbrido:
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/formHibrid1.png)
<sub></br>Del lado del cliente el formulario se vera asi:</br></sub> 
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/formHibrid2.png)
</br>
[<sub>Volver a inicio</sub>](#inicio)

<a name="getcredentials"></a>
####Obtener datos de configuracion
Se puede obtener los datos de configuracion del plugin con solo loguearte con tus credenciales de Todopago. </br>
a. Ir a la opcion Obtener credenciales</br>
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/getcredentials1.png)
b. En el popup loguearse con el mail y password de Todopago.</br>
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/getcredentials2.png)
c. Los datos se cargaran automaticamente en los campos Merchant ID y Security code en el ambiente correspondiente (Desarrollo o produccion ) y solo hay que hacer click en el boton guardar datos y listo.</br>
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/getcredentials3.png)
[<sub>Volver a inicio</sub>](#inicio)

<br />
<a name="maxcuotas"></a>
####Configuración de Maximo de Cuotas
Se puede configurar la cantidad máxima de cuotas que ofrecerá el formulario de TodoPago con el campo cantidad máxima de cuotas. Para que se tenga en cuenta este valor se debe habilitar el campo Habilitar máximo de cuotas y tomará el valor fijado para máximo de cuotas. En caso que esté habilitado el campo y no haya un valor puesto para las cuotas se tomará el valor 12 por defecto.
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/magento/maxcuotas.PNG)
[<sub>Volver a inicio</sub>](#inicio)

<br />
<a name="tca"></a>
#### Nuevas columnas y atributos
El plug in para lograr las nuevas funcionalidades y su persistencia dentro del framework crear&aacute; nuevas tablas, columnas y atributos:

#####Nuevas Columnas:
1. en tabla sales_flat_order: todopagoclave.

#####Nuevos atributos:
1. del tipo "catalog_product": todopagofechaevento, todopagocodigo, todopagoenvio, todopagoservicio, todopagodelivery.
2. del tipo "customer": celular. 

<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="cybersource"></a>
## Prevención de Fraude
- [Consideraciones Generales](#cons_generales)
- [Consideraciones para vertical RETAIL](#cons_retail)

<a name="cons_generales"></a>
####Consideraciones Generales (para todas los verticales, por defecto RETAIL)
El plug in, toma valores est&aacute;ndar del framework para validar los datos del comprador. Principalmente se utiliza una instancia de la clase <em>Mage_Sales_Model_Order</em>.
Para acceder a los datos del comprador se utiliza el metodo getBillingAddress() que devuelve un objeto ya instanciado del cual se usan los siguientes m&eacute;todos:

```php
   $order = new Mage_Sales_Model_Order ();
   $order->load($id);
-- Ciudad de Facturación: $order->getBillingAddress()->getCity();
-- País de facturación: $order->getBillingAddress()->getCountry();
-- Identificador de Usuario: $order->getBillingAddress()->getCustomerId();
-- Email del usuario al que se le emite la factura: $order->getBillingAddress()->getEmail();
-- Nombre de usuario el que se le emite la factura: $order->getBillingAddress()->getFirstname();
-- Apellido del usuario al que se le emite la factura: $order->getBillingAddress()->getLastname();
-- Teléfono del usuario al que se le emite la factura: $order->getBillingAddress()->getTelephone();
-- Provincia de la dirección de facturación: $order->getBillingAddress()->getRegion();
-- Domicilio de facturación: $order->getBillingAddress()->getStreet1();
-- Complemento del domicilio. (piso, departamento): $order->getBillingAddress()->getStreet2();
-- Moneda: $order->getBaseCurrencyCode();
-- Total:  $order->getGrandTotal();
-- IP de la pc del comprador: $order->getRemoteIp();
```
Otros de los modelos utlilizados es <em>Customer</em> del cual a trav&eacute;s  del m&eacute;todo <em>getPasswordHash()</em>, se extrae el password del usuario (comprador) y la tabla <em>sales_flat_invoice_grid</em>, donde se consultan las transacciones facturadas al comprador. 
<a name="cons_retail"></a> 
####Consideraciones para vertical RETAIL
Las consideración para el caso de empresas del rubro <strong>RETAIL</strong> son similares a las <em>consideraciones generales</em> con la diferencia de se utiliza el m&eacute;todo getShippingAddress() que devuelve un objeto y del cual se utilizan los siguientes m&eacute;todos;
```php
-- Ciudad de envío de la orden: $order->getShippingAddress()->getCity();
-- País de envío de la orden: $order->getShippingAddress()->getCountry();
-- Mail del destinatario: $order->getShippingAddress()->getEmail();
-- Nombre del destinatario: $order->getShippingAddress()->getFirstname();
-- Apellido del destinatario: $order->getShippingAddress()->getLastname();
-- Número de teléfono del destinatario: $order->getShippingAddress()->getTelephone();
-- Código postal del domicio de envío: $order->getShippingAddress()->getPostcode();
-- Provincia de envío: $order->getShippingAddress()->getRegion();
-- Domicilio de envío: $order->getShippingAddress()->getStreet1();
-- Método de despacho: $order->getShippingDescription();
-- Código de cupón promocional: $order->getCuponCode();
-- Para todo lo referido productos: $order->getItemsCollection();
```
nota: el valor resultante de $order->getItemsCollection(), se usan como referencias para conseguir informaci&oacute;n del modelo catalog/producto - a través de los métodos <strong>getDescription(), getName(), getSku(), getQtyOrdered(), getPrice()</strong>-.

####Muy Importante
<strong>Provincias:</strong> uno de los datos requeridos para prevención común a todos los verticales  es el campo provinicia/state tanto del comprador como del lugar de envío, para tal fin el plug in utiliza el valor del campo región de las tablas de la orden (sales_flat_order_address) a través del getRegion() tanto del <em>billingAddress</em> como del <em>shippingAddress</em>. El formato de estos datos deben ser tal cual la tabla de referencia (tabla provincias). Para simplificar la implementación de la tabla en magento se deja para su implementación la clase Decidir\Decidirpago\Model\System\Config\Source\Csprovincias.php, con el formato requerido. Al final de este documento se muestra un script sql que pude ser tomado de refrencia.
<br />
<strong>Celular:</strong> el plug in crear&aacute; un nuevo atributo de customer del tipo EAV, con el nombre (Attribute Code) celular que se ver&aacute; en la vista con la etiqueta (label) Celular, el cual en deberá ser implemantado para su utilizaci&oacute;n.
Ejemplo:
```php
$customer = Mage::getModel('customer/customer')->load($customer_id);
$customer->getCelular();
```
En caso que la tienda decida no implementar este nuevo atributo o que el valor quede vac&iacute;o el plug in mandara al sistema el mismo n&uacute;mero que devuleve el m&eacute;todo $order->getBillingAddress()->getTelephone(). 
<a name="prevfraudedatosadicionales" ></a>
####Nuevos Atributos en los productos
Para efectivizar la prevenci&oacute;n de fraude se han creado nuevos atributos de producto dentro de la categoria <em>"Prevenci&oacute;n de Fraude"</em>.</br> 
![imagen nuevos catalogo producto](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/catalogo_producto.PNG)<br />
<sub></sub><br />
![imagen campos cybersource](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/campos_prevenciondefraude.PNG)
<sub></br>Estos campos no son obligatorios aunque si requeridos para Control de Fraude</sub>
<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="features"></a>
## Características
 - [Consulta de transacciones](#constrans)
 - [Devoluciones](#devoluciones)
 
<br />
<a name="constrans" ></a>
#### Consulta de Transacciones
El plug in crea un nuevo <strong>tab</strong> para poder consultar <strong>on line</strong> las características de la transacci&oacute;n en el sistema de Todo Pago.</br>
![imagen consulta de trnasacciones](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/getstatus.PNG)
<br />
[<sub>Volver a inicio</sub>](#inicio)
</br>

<a name="devoluciones"></a>
#### Devoluciones
Es posible realizar devoluciones o reembolsos mediante el procedimiento habitual de Magento. Para ello dirigirse a una orden, y mediante el menú seleccionar "Invoices" para poder generar una nota de crédito (credit memo) sobre la factura. Allí deberá hacerse click en el botón "Refund" para que la devolución sea online y procesada por Todo Pago.</br>
![imagen devoluciones](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/magedevol.png)
<br />
[<sub>Volver a inicio</sub>](#inicio)


<a name="tablas"></a>
## Tablas de Referencia
######[Provincias](#p)
######[Script SQL provincias](#sp)

<a name="p"></a>
<p>Provincias</p>
<table>
<tr><th>Provincia</th><th>Código</th></tr>
<tr><td>CABA</td><td>C</td></tr>
<tr><td>Buenos Aires</td><td>B</td></tr>
<tr><td>Catamarca</td><td>K</td></tr>
<tr><td>Chaco</td><td>H</td></tr>
<tr><td>Chubut</td><td>U</td></tr>
<tr><td>Córdoba</td><td>X</td></tr>
<tr><td>Corrientes</td><td>W</td></tr>
<tr><td>Entre Ríos</td><td>E</td></tr>
<tr><td>Formosa</td><td>P</td></tr>
<tr><td>Jujuy</td><td>Y</td></tr>
<tr><td>La Pampa</td><td>L</td></tr>
<tr><td>La Rioja</td><td>F</td></tr>
<tr><td>Mendoza</td><td>M</td></tr>
<tr><td>Misiones</td><td>N</td></tr>
<tr><td>Neuquén</td><td>Q</td></tr>
<tr><td>Río Negro</td><td>R</td></tr>
<tr><td>Salta</td><td>A</td></tr>
<tr><td>San Juan</td><td>J</td></tr>
<tr><td>San Luis</td><td>D</td></tr>
<tr><td>Santa Cruz</td><td>Z</td></tr>
<tr><td>Santa Fe</td><td>S</td></tr>
<tr><td>Santiago del Estero</td><td>G</td></tr>
<tr><td>Tierra del Fuego</td><td>V</td></tr>
<tr><td>Tucumán</td><td>T</td></tr>
</table>
[<sub>Volver a inicio</sub>](#inicio)

<a name="sp"></a>
```sql
INSERT INTO
directory_country_region (region_id, country_id , code, default_name)
VALUES
('4859', 'AR', 'C', 'CABA'),
('486' , 'AR', 'B', 'Buenos Aires'),
('487' , 'AR', 'A', 'Salta'),
('488' , 'AR', 'K', 'Catamarca'),
('489' , 'AR', 'H', 'Chaco'),
('490' , 'AR', 'U', 'Chubut'),
('491' , 'AR', 'X', 'Cordoba'),
('492' , 'AR', 'W', 'Corrientes'),
('493' , 'AR', 'E', 'Entre Rios'),
('494' , 'AR', 'P', 'Formosa'),
('495' , 'AR', 'Y', 'Jujuy'),
('496' , 'AR', 'L', 'La Pampa'),
('497' , 'AR', 'F', 'La Rioja'),
('498' , 'AR', 'M', 'Mendoza'),
('499' , 'AR', 'N', 'Misiones'),
('500' , 'AR', 'Q', 'Neuquen'),
('501' , 'AR', 'R', 'Rio Negro'),
('502' , 'AR', 'J', 'San Juan'),
('503' , 'AR', 'D', 'San Luis'),
('504' , 'AR', 'S', 'Santa Fe'),
('505' , 'AR', 'G', 'Santiago del Estero'),
('506' , 'AR', 'V', 'Tierra del Fuego'),
('507' , 'AR', 'T', 'Tucuman'),
('5071', 'AR', 'Z', 'Santa Cruz');

INSERT INTO
directory_country_region_name (locale, region_id , name)
VALUES
('en_US', '4859', 'CABA'),
('en_US', '486', 'Buenos Aires'),
('en_US', '487', 'Salta'),
('en_US', '488', 'Catamarca'),
('en_US', '489', 'Chaco'),
('en_US', '490', 'Chubut'),
('en_US', '491', 'Cordoba'),
('en_US', '492', 'Corrientes'),
('en_US', '493', 'Entre Rios'),
('en_US', '494', 'Formosa'),
('en_US', '495', 'Jujuy'),
('en_US', '496', 'La Pampa'),
('en_US', '497', 'La Rioja'),
('en_US', '498', 'Mendoza'),
('en_US', '499', 'Misiones'),
('en_US', '500', 'Neuquen'),
('en_US', '501', 'Rio Negro'),
('en_US', '502', 'San Juan'),
('en_US', '503', 'San Luis'),
('en_US', '504', 'Santa Fe'),
('en_US', '505', 'Santiago del Estero'),
('en_US', '506', 'Tierra del Fuego'),
('en_US', '507', 'Tucuman'),
('en_US','5071', 'Santa Cruz');
```

<a name="availableversions"></a>
## Versiones Disponibles##
<table>
  <thead>
    <tr>
      <th>Version del Plugin</th>
      <th>Estado</th>
      <th>Versiones Compatibles</th>
    </tr>
  <thead>
  <tbody>
    <tr>
      <td><a href="https://github.com/TodoPago/Plugin-Magento/archive/master.zip">v1.7.x - v1.9.x</a></td>
      <td>Stable (Current version)</td>
      <td>Community Edition 1.6.x - 1.9.x<br />
          Enterprise Edition 1.11.x - 1.14.x
      </td>
    </tr>
  </tbody>
</table>

*Click on the links above for instructions on installing and configuring the module.*


[<sub>Volver a inicio</sub>](#inicio)

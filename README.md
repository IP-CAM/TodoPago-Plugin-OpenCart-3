<a name="inicio"></a>
# OpenCart 3
### Versión 1.2.0


Plug in para la integración con gateway de pago <strong>Todo Pago</strong>
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalación](#instalacion)
- [Configuración](#configuracion)
	- [Configuración plug in](#confplugin)
	- [Configuración de Maximo de Cuotas](#maxcuotas)
	- [Configuración de tiempo de vida del formulario de pago](#timeout)
	- [Formulario Hibrido](#formHibrido)
	- [Validación por Google Maps](#googlemaps)
	- [Vaciado del carrito](#vaciadocarrito)
	- [Obtener datos de configuracion](#getcredentials)
- [Datos adiccionales para prevención de fraude](#PrevencionFraude)
- [Consulta de transacciones](#constrans)
- [Devoluciones](#devoluciones)
- [Tablas de referencia](#tablas)
- [Tabla de errores](#codigoerrores)

[](#consideracionesgenerales)
## Consideraciones Generales
El plug in de pagos de <strong>Todo Pago</strong>, provee a las tiendas OpenCart de un nuevo método de pago, integrando la tienda al gateway de pago.
La versión de este plug in esta testeada en PHP 5.6 y Opencart 3.0.2.0.

Por requerimientos del medio de pago el plugin configurará un código postal como obligatorio para Argentina. Los clientes que NO tengan código postal configurado NO podrán pagar.

<a name="instalacion"></a>
## Instalación
1. Descomprimir el archivo opencart-plugin-master.zip. 
2. Copiar carpeta 'catalog', 'admin' y 'system al root de opencart con los mismos nombres.
3. Verificar que al menos el usuario administrador tenga los permisos de acceso y modificación de <strong>payment/todopago</strong>.
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart20/opencart2_usergroup.PNG)
4.	Luego ir a 'extensions->payments' e ir a la opción Todo Pago e instalar.
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/opencart2_configuracion.PNG)

Observación:
Descomentar: <em>extension=php_soap.dll</em> (y extension=php_mbstring en caso de ser necesario) del php.ini, ya que para la conexión al gateway se utiliza la clase <em>SoapClient</em> del API de PHP.

[<sub>Volver a inicio</sub>](#inicio)

<a name="configuracion"></a>
## Configuración

<a name="confplugin"></a>
#### Configuración plug in
Para llegar al menu de configuración ir a: <em>System->Configuration</em> y seleccionar Paymenth Method en el menú izquierdo.
Una vez instalado el plug in, ir a 'extensions->payments' y ir a la opción Todo Pago, hacer click en el lápiz, para comenzar a configurar.
La configuración del Plug in esta dividido en 5 solapas (GENERAL, AMBIENTE TEST, AMBIENTE PRODUCCIÓN, ESTADO DEL PEDIDO) y una solapa adiccional (Status de la Operación) que se utiliza para ver el estado de la orden (transacción) de manera on line.
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/opencart2_conf_solapas.PNG)
</br>

<a name="maxcuotas"></a>
#### Configuración de Maximo de Cuotas
Se puede configurar la cantidad máxima de cuotas que ofrecerá el formulario de TodoPago con el campo cantidad máxima de cuotas. Para que se tenga en cuenta este valor se debe habilitar el campo Habilitar máximo de cuotas y tomará el valor fijado para máximo de cuotas. En caso que esté habilitado el campo y no haya un valor puesto para las cuotas se tomará el valor 12 por defecto.
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart20/open2-maxinstallments.png)

[<sub>Volver a inicio</sub>](#inicio)

<a name="timeout"></a>
#### Configuración de tiempo de vida del formulario
En la configuracion del plugin se puede setear el tiempo maximo en el que se puede realizar el pago del formulario en milisegundos. Por defecto si no se envia, 1800000 (30 minutos)
Valor minimo: 300000 (5 minutos)
Valor maximo: 21600000 (6hs)
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart20/open2-timeout.png)</br>
[<sub>Volver a inicio</sub>](#inicio)

<a name="maxcuotas"></a>
#### Configuración de Maximo de Cuotas

Se puede configurar la cantidad máxima de cuotas que ofrecerá el formulario de TodoPago con el campo cantidad máxima de cuotas. Para que se tenga en cuenta este valor se debe habilitar el campo Habilitar máximo de cuotas y tomará el valor fijado para máximo de cuotas. En caso que esté habilitado el campo y no haya un valor puesto para las cuotas se tomará el valor 12 por defecto.
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart20/open2-maxinstallments.png)
[<sub>Volver a inicio</sub>](#inicio)

<a name="formHibrido"></a>
#### Formulario Híbrido

Se incluyen dos tipos de formularios de pago, redirección y Formulario Híbrido (embebido en el e commerce). Para utilizar este último se debe seleccionar Híbrido en la configuración general del Plugin. 
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart3/oc3-config-formulario.png)
El formulario seleccionado se mostrara en la etapa final del proceso de pago "Confirmar pago".
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart3/oc3-form-hibrido2.png)
Existen dos formas de pago, ingresando los datos de una tarjeta o utilizando la billetera de Todopago. Al ir a "Pagar con Billetera" desplegara una ventana que permitira ingresar a billetera y realizar el pago.
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart3/oc3-billetera-2.png)

<a name="googlemaps"></a>
#### Google Maps
Es posible validar la dirección ingresada por el usuario antes de enviar al control de fraude. Basta con activar la opción en la configuración del panel para que el plugin envíe los datos a Google Maps previo a pedir autorización.
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart3/oc3-config-gmaps.png)

<a name="vaciadocarrito"></a>
#### Vaciar Carrito
Se puede configurar al carrito de compras para ser vaciado en caso de haber un error al validar los datos del cliente. Esto también se configura desde el mismo panel de administrador.
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart3/oc3-config-carrito.png)

<a name="getcredentials"></a>
#### Obtener datos de configuración
En el popup loguearse con el mail y password de Todopago. Los datos se cargaran automaticamente en los campos Authorization HTTP y Id Site Todo Pago code en el ambiente correspondiente (Desarrollo o producción) y solo hay que hacer click en el botón guardar datos y listo.
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart20/Selección_016.png)

<sub><em>Menú principal</em></sub>

<a name="confplanes"></a>

[<sub>Volver a inicio</sub>](#inicio)
<a name="tca"></a>
## Nuevas columnas y atributos
El plug in para lograr las nuevas funcionalidades y su persistencia dentro del framework crear&aacute; nuevas tablas, columnas y atributos:

##### Nuevas Columnas:
1. en tabla order: todopagoclave.

##### Nuevos atributos:
1. del tipo "attribute": fecha evento, codigo del producto, Tipo de envio, Tipo de servicio, Tipo de delivery.
Los valores posiblespara cada uno de estos atributos deben ser:
![imagen nuevos catalogo producto](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/open_attributes.PNG)

- Para **"tipo de envio"** (solo para *Ticketing*): Pick up, Email, Smartphone o Other
- Para **"tipo de Servicio"** (solo para vertical *Servicios*): Luz, Gas, Telefono, Agua, TV, Cable, Internet, Impuestos.
- Para **"tipo de delivery"** (solo para vertical *Digital goods*): WEB Session, Email o SmartPhone.

[<sub>Volver a inicio</sub>](#inicio)

<a name="PrevencionFraude"></a>
## Prevención de Fraude
- [Consideraciones Generales](#cons_generales)
- [Consideraciones para vertical RETAIL](#cons_retail)

<a name="cons_generales"></a>
#### Consideraciones Generales (para todas las verticales, por defecto RETAIL)
El plug in, toma valores est&aacute;ndar del framework para validar los datos del comprador. Principalmente se utiliza una instancia de la clase *checkout/order*.
Para acceder a los datos del comprador se utiliza el metodo $this->model_checkout_order->getOrder($order_id) que devuelve un array:

```php
   $this->load->model('checkout/order');
   $order = $this->model_checkout_order->getOrder($order_id);
-- Ciudad de Facturación: $order['payment_city'];
-- País de facturación: $order['payment_iso_code_2'];
-- Identificador de Usuario: $order['customer_id'];
-- Email del usuario al que se le emite la factura: $order['email'];
-- Nombre de usuario el que se le emite la factura: $order['payment_firstname'];
-- Apellido del usuario al que se le emite la factura: $order['payment_lastname'];
-- Teléfono del usuario al que se le emite la factura: $order['telephone'];
-- Provincia de la dirección de facturación: $order['payment_zone_code'];
-- Domicilio de facturación: $order['payment_address_1'];;
-- Complemento del domicilio. (piso, departamento): $order['payment_address_2'];
-- Moneda: $order['currency_code'];
-- Total:  $order['total'];
-- IP de la pc del comprador: $order['ip'];
```
Otros de los modelos utlilizados es <em>Customer</em> del cual a trav&eacute;s  del m&eacute;todo <em>getPasswordHash()</em>, se extrae el password del usuario (comprador) y la tabla <em>sales_flat_invoice_grid</em>, donde se consultan las transacciones facturadas al comprador. 

<a name="cons_retail"></a> 
#### Consideraciones para vertical RETAIL
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
Nota: el valor resultante de $order->getItemsCollection(), se usan como referencias para conseguir informaci&oacute;n del modelo catalog/producto - a través de los métodos <strong>getDescription(), getName(), getSku(), getQtyOrdered(), getPrice()</strong>-.

#### Muy Importante
<strong>Provincias:</strong> uno de los datos requeridos para prevención común a todos los verticales  es el campo provinicia/state tanto del comprador como del lugar de envío, para tal fin el plug in utiliza el valor del campo región de las tablas de la orden.

[<sub>Volver a inicio</sub>](#inicio)

<a name="constrans"></a>
## Consulta de Transacciones
El plug in crea un nuevo <strong>tab</strong> para poder consultar <strong>on line</strong> las características de la transacci&oacute;n en el sistema de Todo Pago.
![imagen consulta de trnasacciones](https://raw.githubusercontent.com/TodoPago/imagenes/master/README.img/opencart2_statusdelaoperacion.PNG)<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="devoluciones"></a>
## Devoluciones
En la pestaña de status de las operaciones, en la tabla de operaciones, la ultima columna de utiliza para realizar operaciones online. Solo debe hacer click en la orden que de desea devolver y en un pop-up de colocara el monto que se desea devolver. A continuación se vera un mensaje de éxito o error según corresponda y sera agregada una nueva operación en el panel de devoluciones de OpenCart.

![imagen consulta de trnasacciones](https://raw.githubusercontent.com/TodoPago/imagenes/master/opencart20/Seleccion_006.png)<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="tablas"></a>
## Tablas de Referencia
###### [Provincias](#p)

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
<tr><td>Entre Ríos</td><td>R</td></tr>
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

<a name="codigoerrores"></a>
## Tabla de errores operativos

<table>
<tr><th>Id mensaje</th><th>Mensaje</th></tr>
<tr><td>-1</td><td>Aprobada.</td></tr>
<tr><td>1100</td><td>El monto ingresado es menor al mínimo permitido</td></tr>
<tr><td>1101</td><td>El monto ingresado supera el máximo permitido.</td></tr>
<tr><td>1102</td><td>Tu tarjeta no corresponde con el banco seleccionado. Iniciá nuevamente la compra.</td></tr>
<tr><td>1104</td><td>El precio ingresado supera al máximo permitido.</td></tr>
<tr><td>1105</td><td>El precio ingresado es menor al mínimo permitido.</td></tr>
<tr><td>1070</td><td>El plazo para realizar esta devolución caducó.</td></tr>
<tr><td>1081</td><td>El saldo de tu cuenta es insuficiente para realizar esta devolución.</td></tr>
<tr><td>2010</td><td>En este momento la operación no pudo ser realizada. Por favor intentá más tarde. Volver a Resumen.</td></tr>
<tr><td>2031</td><td>En este momento la validación no pudo ser realizada, por favor intentá más tarde.</td></tr>
<tr><td>2050</td><td>Tu compra no puede ser realizada. Comunicate con tu vendedor.</td></tr>
<tr><td>2051</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor.</td></tr>
<tr><td>2052</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor. </td></tr>
<tr><td>2053</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor.</td></tr>
<tr><td>2054</td><td>El producto que querés comprar se encuentra agotado. Por favor contactate con tu vendedor.</td></tr>
<tr><td>2056</td><td>Tu compra no pudo ser procesada.</td></tr>
<tr><td>2057</td><td>La operación no pudo ser procesada. Por favor intentá más tarde.</td></tr>
<tr><td>2058</td><td>La operación fué rechazada. Comunicate con el 0800 333 0010.</td></tr>
<tr><td>2059</td><td>La operación no pudo ser procesada. Por favor intentá más tarde.</td></tr>
<tr><td>2062</td><td>Tu compra no puede ser realizada. Comunicate con tu vendedor.</td></tr>
<tr><td>2064</td><td>Tu compra no puede ser realizada. Comunicate con tu vendedor.</td></tr>
<tr><td>2074</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>2075</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>2076</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>90000</td><td>La cuenta destino de los fondos es inválida. Verificá la información ingresada en Mi Perfil.</td></tr>
<tr><td>90001</td><td>La cuenta ingresada no pertenece al CUIT/ CUIL registrado.</td></tr>
<tr><td>90002</td><td>No pudimos validar tu CUIT/CUIL.  Comunicate con nosotros <a href="#contacto" target="_blank">acá</a> para más información.</td></tr>
<tr><td>99900</td><td>Tu compra fue exitosa.</td></tr>
<tr><td>99901</td><td>Tu Billetera Virtual no tiene medios de pago adheridos. Ingresá a tu cuenta de Todo Pago y cargá tus tarjetas.</td></tr>
<tr><td>99902</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99903</td><td>Lo sentimos, hubo un error al procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99904</td><td>El saldo de tu tarjeta no te permite realizar esta compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99905</td><td>En este momento la operación no pudo ser procesada. Intentá nuevamente.</td></tr>
<tr><td>99907</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor. </td></tr>
<tr><td>99910</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99911</td><td>Lo sentimos, se terminó el tiempo para confirmar esta compra. Por favor iniciá nuevamente el proceso de pago.</td></tr>
<tr><td>99950</td><td>Tu compra no pudo ser procesada.</td></tr>
<tr><td>99960</td><td>Esta compra requiere autorización de VISA. Comunicate al número que se encuentra al dorso de tu tarjeta.</td></tr>
<tr><td>99961</td><td>Esta compra requiere autorización de AMEX. Comunicate al número que se encuentra al dorso de tu tarjeta.</td></tr>
<tr><td>99970</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99971</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99972</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99974</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99975</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99977</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99979</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99978</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99979</td><td>Lo sentimos, el pago no pudo ser procesado.</td></tr>
<tr><td>99980</td><td>Ya realizaste una compra por el mismo importe. Iniciala nuevamente en unos minutos.</td></tr>
<tr><td>99982</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando.</td></tr>
<tr><td>99983</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99984</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99985</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99986</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99987</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99988</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99989</td><td>Tu tarjeta no autorizó tu compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99990</td><td>Tu tarjeta está vencida. Iniciá nuevamente la compra utilizando otro medio de pago.</td></tr>
<tr><td>99991</td><td>Los datos informados son incorrectos. Por favor ingresalos nuevamente.</td></tr>
<tr><td>99992</td><td>El saldo de tu tarjeta no te permite realizar esta compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99993</td><td>Tu tarjeta no autorizó el pago. Iniciá nuevamente la compra utilizando otro medio de pago.</td></tr>
<tr><td>99994</td><td>El saldo de tu tarjeta no te permite realizar esta operacion.</td></tr>
<tr><td>99995</td><td>Tu tarjeta no autorizó tu compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99996</td><td>La operación fué rechazada por el medio de pago porque el monto ingresado es inválido.</td></tr>
<tr><td>99997</td><td>Lo sentimos, en este momento la operación no puede ser realizada. Por favor intentá más tarde.</td></tr>
<tr><td>99998</td><td>Tu tarjeta no autorizó tu compra. Iniciala nuevamente utilizando otro medio de pago.
<tr><td>99999</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
</table>

[<sub>Volver a inicio</sub>](#inicio)

<a name="interrores"></a>
## Tabla de errores de integración

<table>
<tr><td>**Id mensaje**</td><td>**Descripción**</td></tr>
<tr><td>98001 </td><td>ERROR: El campo CSBTCITY es requerido</td></tr>
<tr><td>98002 </td><td>ERROR: El campo CSBTCOUNTRY es requerido</td></tr>
<tr><td>98003 </td><td>ERROR: El campo CSBTCUSTOMERID es requerido</td></tr>
<tr><td>98004 </td><td>ERROR: El campo CSBTIPADDRESS es requerido</td></tr>
<tr><td>98005 </td><td>ERROR: El campo CSBTEMAIL es requerido</td></tr>
<tr><td>98006 </td><td>ERROR: El campo CSBTFIRSTNAME es requerido</td></tr>
<tr><td>98007 </td><td>ERROR: El campo CSBTLASTNAME es requerido</td></tr>
<tr><td>98008 </td><td>ERROR: El campo CSBTPHONENUMBER es requerido</td></tr>
<tr><td>98009 </td><td>ERROR: El campo CSBTPOSTALCODE es requerido</td></tr>
<tr><td>98010 </td><td>ERROR: El campo CSBTSTATE es requerido</td></tr>
<tr><td>98011 </td><td>ERROR: El campo CSBTSTREET1 es requerido</td></tr>
<tr><td>98012 </td><td>ERROR: El campo CSBTSTREET2 es requerido</td></tr>
<tr><td>98013 </td><td>ERROR: El campo CSPTCURRENCY es requerido</td></tr>
<tr><td>98014 </td><td>ERROR: El campo CSPTGRANDTOTALAMOUNT es requerido</td></tr>
<tr><td>98015 </td><td>ERROR: El campo CSMDD7 es requerido</td></tr>
<tr><td>98016 </td><td>ERROR: El campo CSMDD8 es requerido</td></tr>
<tr><td>98017 </td><td>ERROR: El campo CSMDD9 es requerido</td></tr>
<tr><td>98018 </td><td>ERROR: El campo CSMDD10 es requerido</td></tr>
<tr><td>98019 </td><td>ERROR: El campo CSMDD11 es requerido</td></tr>
<tr><td>98020 </td><td>ERROR: El campo CSSTCITY es requerido</td></tr>
<tr><td>98021 </td><td>ERROR: El campo CSSTCOUNTRY es requerido</td></tr>
<tr><td>98022 </td><td>ERROR: El campo CSSTEMAIL es requerido</td></tr>
<tr><td>98023 </td><td>ERROR: El campo CSSTFIRSTNAME es requerido</td></tr>
<tr><td>98024 </td><td>ERROR: El campo CSSTLASTNAME es requerido</td></tr>
<tr><td>98025 </td><td>ERROR: El campo CSSTPHONENUMBER es requerido</td></tr>
<tr><td>98026 </td><td>ERROR: El campo CSSTPOSTALCODE es requerido</td></tr>
<tr><td>98027 </td><td>ERROR: El campo CSSTSTATE es requerido</td></tr>
<tr><td>98028 </td><td>ERROR: El campo CSSTSTREET1 es requerido</td></tr>
<tr><td>98029 </td><td>ERROR: El campo CSMDD12 es requerido</td></tr>
<tr><td>98030 </td><td>ERROR: El campo CSMDD13 es requerido</td></tr>
<tr><td>98031 </td><td>ERROR: El campo CSMDD14 es requerido</td></tr>
<tr><td>98032 </td><td>ERROR: El campo CSMDD15 es requerido</td></tr>
<tr><td>98033 </td><td>ERROR: El campo CSMDD16 es requerido</td></tr>
<tr><td>98034 </td><td>ERROR: El campo CSITPRODUCTCODE es requerido</td></tr>
<tr><td>98035 </td><td>ERROR: El campo CSITPRODUCTDESCRIPTION es requerido</td></tr>
<tr><td>98036 </td><td>ERROR: El campo CSITPRODUCTNAME es requerido</td></tr>
<tr><td>98037 </td><td>ERROR: El campo CSITPRODUCTSKU es requerido</td></tr>
<tr><td>98038 </td><td>ERROR: El campo CSITTOTALAMOUNT es requerido</td></tr>
<tr><td>98039 </td><td>ERROR: El campo CSITQUANTITY es requerido</td></tr>
<tr><td>98040 </td><td>ERROR: El campo CSITUNITPRICE es requerido</td></tr>
<tr><td>98101 </td><td>ERROR: El formato del campo CSBTCITY es incorrecto</td></tr>
<tr><td>98102 </td><td>ERROR: El formato del campo CSBTCOUNTRY es incorrecto</td></tr>
<tr><td>98103 </td><td>ERROR: El formato del campo CSBTCUSTOMERID es incorrecto</td></tr>
<tr><td>98104 </td><td>ERROR: El formato del campo CSBTIPADDRESS es incorrecto</td></tr>
<tr><td>98105 </td><td>ERROR: El formato del campo CSBTEMAIL es incorrecto</td></tr>
<tr><td>98106 </td><td>ERROR: El formato del campo CSBTFIRSTNAME es incorrecto</td></tr>
<tr><td>98107 </td><td>ERROR: El formato del campo CSBTLASTNAME es incorrecto</td></tr>
<tr><td>98108 </td><td>ERROR: El formato del campo CSBTPHONENUMBER es incorrecto</td></tr>
<tr><td>98109 </td><td>ERROR: El formato del campo CSBTPOSTALCODE es incorrecto</td></tr>
<tr><td>98110 </td><td>ERROR: El formato del campo CSBTSTATE es incorrecto</td></tr>
<tr><td>98111 </td><td>ERROR: El formato del campo CSBTSTREET1 es incorrecto</td></tr>
<tr><td>98112 </td><td>ERROR: El formato del campo CSBTSTREET2 es incorrecto</td></tr>
<tr><td>98113 </td><td>ERROR: El formato del campo CSPTCURRENCY es incorrecto</td></tr>
<tr><td>98114 </td><td>ERROR: El formato del campo CSPTGRANDTOTALAMOUNT es incorrecto</td></tr>
<tr><td>98115 </td><td>ERROR: El formato del campo CSMDD7 es incorrecto</td></tr>
<tr><td>98116 </td><td>ERROR: El formato del campo CSMDD8 es incorrecto</td></tr>
<tr><td>98117 </td><td>ERROR: El formato del campo CSMDD9 es incorrecto</td></tr>
<tr><td>98118 </td><td>ERROR: El formato del campo CSMDD10 es incorrecto</td></tr>
<tr><td>98119 </td><td>ERROR: El formato del campo CSMDD11 es incorrecto</td></tr>
<tr><td>98120 </td><td>ERROR: El formato del campo CSSTCITY es incorrecto</td></tr>
<tr><td>98121 </td><td>ERROR: El formato del campo CSSTCOUNTRY es incorrecto</td></tr>
<tr><td>98122 </td><td>ERROR: El formato del campo CSSTEMAIL es incorrecto</td></tr>
<tr><td>98123 </td><td>ERROR: El formato del campo CSSTFIRSTNAME es incorrecto</td></tr>
<tr><td>98124 </td><td>ERROR: El formato del campo CSSTLASTNAME es incorrecto</td></tr>
<tr><td>98125 </td><td>ERROR: El formato del campo CSSTPHONENUMBER es incorrecto</td></tr>
<tr><td>98126 </td><td>ERROR: El formato del campo CSSTPOSTALCODE es incorrecto</td></tr>
<tr><td>98127 </td><td>ERROR: El formato del campo CSSTSTATE es incorrecto</td></tr>
<tr><td>98128 </td><td>ERROR: El formato del campo CSSTSTREET1 es incorrecto</td></tr>
<tr><td>98129 </td><td>ERROR: El formato del campo CSMDD12 es incorrecto</td></tr>
<tr><td>98130 </td><td>ERROR: El formato del campo CSMDD13 es incorrecto</td></tr>
<tr><td>98131 </td><td>ERROR: El formato del campo CSMDD14 es incorrecto</td></tr>
<tr><td>98132 </td><td>ERROR: El formato del campo CSMDD15 es incorrecto</td></tr>
<tr><td>98133 </td><td>ERROR: El formato del campo CSMDD16 es incorrecto</td></tr>
<tr><td>98134 </td><td>ERROR: El formato del campo CSITPRODUCTCODE es incorrecto</td></tr>
<tr><td>98135 </td><td>ERROR: El formato del campo CSITPRODUCTDESCRIPTION es incorrecto</td></tr>
<tr><td>98136 </td><td>ERROR: El formato del campo CSITPRODUCTNAME es incorrecto</td></tr>
<tr><td>98137 </td><td>ERROR: El formato del campo CSITPRODUCTSKU es incorrecto</td></tr>
<tr><td>98138 </td><td>ERROR: El formato del campo CSITTOTALAMOUNT es incorrecto</td></tr>
<tr><td>98139 </td><td>ERROR: El formato del campo CSITQUANTITY es incorrecto</td></tr>
<tr><td>98140 </td><td>ERROR: El formato del campo CSITUNITPRICE es incorrecto</td></tr>
<tr><td>98201 </td><td>ERROR: Existen errores en la información de los productos</td></tr>
<tr><td>98202 </td><td>ERROR: Existen errores en la información de CSITPRODUCTDESCRIPTION los productos</td></tr>
<tr><td>98203 </td><td>ERROR: Existen errores en la información de CSITPRODUCTNAME los productos</td></tr>
<tr><td>98204 </td><td>ERROR: Existen errores en la información de CSITPRODUCTSKU los productos</td></tr>
<tr><td>98205 </td><td>ERROR: Existen errores en la información de CSITTOTALAMOUNT los productos</td></tr>
<tr><td>98206 </td><td>ERROR: Existen errores en la información de CSITQUANTITY los productos</td></tr>
<tr><td>98207 </td><td>ERROR: Existen errores en la información de CSITUNITPRICE de los productos</td></tr>
</table>

[<sub>Volver a inicio</sub>](#inicio)

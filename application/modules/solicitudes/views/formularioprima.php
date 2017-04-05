<h5 style="font-size:14px">Prima e informaci&oacute;n de cobros</h5>
<hr style="margin-top:10px!important;"> 
<input type="hidden" id="ramoscadena" v-model="ramoscadena" >   
<div class="row">
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
        <label>Prima Anual </label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" name="campoprima[prima_anual]" v-model="primaAnual" class="form-control"  id="prima_anual" data-rule-required="true">
        </div>                          
    </div>
    <div class="form-group col-xs-6 col-sm-3 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
        <br /> -
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
        <label>Descuentos </label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" name="campoprima[descuentos]" v-model="descuentosPrima" class="form-control"  id="descuentos">
        </div>                                
    </div>    
    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
        <br /> +
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
        <label>Otros </label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" name="campoprima[otros]" v-model="otrosPrima"  class="form-control"  id="otros">
        </div>                                
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
        <br /> +
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
        <label>Impuesto </label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" name="campoprima[impuesto]" v-model="impuestoMonto" data-rule-required="true" class="form-control"  id="impuesto" readonly>
        </div>                                
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
        <br /> =
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
        <label>Total </label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" name="campoprima[total]" v-model="totalPrima" class="form-control"  id="total" readonly="">
        </div>                                
    </div>
</div> 
<div class="row">
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
        <label>Sitio de pago <span required="" aria-required="true">*</span> </label>
        <select  name="campoprima[sitio_pago]" class="form-control" id="sitiopago" data-rule-required="true">
            <option value="">Seleccione</option>
            <option v-for="sitio in catalogoSitioPago" v-bind:value="sitio.valor" :selected="sitio.valor == prima.sitio_pago">{{{sitio.etiqueta}}}</option>
        </select>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan">
        <label>M&eacute;todo de pago <span required="" aria-required="true">*</span> </label>
        <select  name="campoprima[metodo_pago]" class="form-control" id="metodopago" data-rule-required="true">
            <option value="">Seleccione</option>
            <option v-for="metodoPago in catalogoMetodoPago" v-bind:value="metodoPago.valor" :selected="metodoPago.valor == prima.metodo_pago">{{{metodoPago.etiqueta}}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-4 col-md-3 col-lg-3">
        <label>Fecha primer Pago <span required="" aria-required="true">*</span> </label>
        <div class="input-group" >
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
            <input type="input" id="fecha_primer_pago" name="campoprim[fecha_primer_pago]"  class="form-control" value=" " data-rule-required="true">
            <input type="hidden" id="fecha_primerPago" name="fecha_primer_pago"  class="form-control" value=" " data-rule-required="true">
        </div>
    </div>
</div> 
<div class="row">
    <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2 plan">
        <label>Cantidad de pagos <span required="" aria-required="true">*</span> </label>
        <select  name="campoprima[cantidad_pagos]" class="form-control" id="cantidadpagos" data-rule-required="true">
            <option value="">Seleccione</option>
            <option v-for="canpag in catalogoCantidadPagos" v-bind:value="canpag.valor" :selected="canpag.etiqueta == prima.cantidad_pagos">{{canpag.etiqueta}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
        <label>Frecuencia de pagos <span required="" aria-required="true">*</span> </label>
        <select  name="campoprima[frecuencia_pago]" class="form-control" id="frecuenciapagos" data-rule-required="true">
            <option value="">Seleccione</option>
            <option v-for="frecuencia in catalogoFrecuenciaPagos" v-bind:value="frecuencia.valor" :selected="frecuencia.valor == prima.frecuencia_pago">{{frecuencia.etiqueta}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
        <label>Centro de facturaci&oacute;n  </label>
        <select  name="campoprima[centro_facturacion]" class="form-control" id="centro_facturacion" @change="clienteDireccion()" data-rule-required="true">
            <option value="">Seleccione</option>
            <option v-for="centroFac in catalogoCentroFacturacion" v-bind:value="centroFac.id" :selected="centroFac.id == prima.centro_facturacion">{{{centroFac.nombre}}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">  
        <label for="direccion">Dirección</label>
        <div id="participacion"></div><input name="campoprima[direccion_pago]" type="text" v-model="clienteCentro" class="form-control" value="{{prima.direccion_pago != false ? prima.direccion_pago : '' ;}}"/>
    </div>
</div>                         
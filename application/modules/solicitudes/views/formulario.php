<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formClienteCrear',
    'autocomplete' => 'off'
);
?>
    <div id="vistaCliente" class="">

        <div class="tab-content">
            <?php echo form_open(base_url('solicitudes/guardar'), $formAttr); ?>
            <div id="datosdelcliente-5" class="tab-pane active col-lg-12 col-md-12">
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" value="">
       
                <div class="ibox">                    
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1" style="color:#337ab7!important;">Datos generales</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-2" style="color:#337ab7!important;">Plan</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-3" style="color:#337ab7!important;">Interés Asegurado</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-4" style="color:#337ab7!important;">Prima y cobros</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-5" style="color:#337ab7!important;">Dist. de comisiones</a></li>
                        </ul>
                    </div>
                
                    <div class="ibox-content" style="display: block;" id="datosGenerales">
                        <div class="row">
                            <h5 style="font-size:14px">Datos generales</h5>
            <hr style="margin-top:10px!important;"> 
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 NombredelCliente "><label>Nombre del Cliente <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[nombre]" value="{{clienteInfo.nombre}}" class="form-control" id="campo[nombre]" data-rule-required="true" disabled>
                                <input name="campo[cliente_id]" type="hidden" value="{{clienteInfo.id}}" />
                                <input name="campo[ramo]" type="hidden" v-model="ramo" />
                                <input name="campo[id_tipo_poliza]" type="hidden" v-model="tipoPoliza" />
                                <input name="codigo_ramo" type="hidden" v-model="codigoRamo" />
                            </div>
                            <!--seccion de identificacion del cliente-->      
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 Identificacíon "><label>Identificación  </label>
                                <select  name="campo[tipo_identificacion]" class="form-control" id="tipo_identificacion" disabled>
                                    <option value="">Seleccione</option>                                    
                                    <option value="{{clienteInfo.tipo_identificacion}}" selected>{{clienteInfo.tipo_identificacion}}</option>
                                </select>
                            </div>               
                                <!--div tipo == pasaporte-->
                                <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2" v-if="clienteInfo.letra == 'PAS'">
                                    <label>Pasaporte</label>
                                    <input data-rule-required="true" type="text" name="pasaporte[pasaporte]" id="pasaporte[pasaporte]" value="{{clienteInfo.identificacion}}" class="form-control" disabled>
                                </div>

                                <!-- div oculto para enseñar campos juridico natural -->
                                <div v-if="clienteInfo.tipo_identificacion == 'juridico'">
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
                                        <label>Tomo/Rollo <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" name="juridico[tomo]" id="juridico[tomo]" value="{{clienteInfo.identificacion.split('-')[0]}}"  class="form-control" disabled>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
                                        <label>Folio/Imagen/Documento <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" name="juridico[folio]" value="{{clienteInfo.identificacion.split('-')[1]}}" class="form-control" disabled>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
                                        <label>Asiento/Ficha <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" value="{{clienteInfo.identificacion.split('-')[2]}}" id="juridico[asiento]" class="form-control" disabled>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
                                        <label>Digito verificador <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" value="{{clienteInfo.identificacion.split('-')[3]}}" name="juridico[verificador]" id="juridico[verificador]" class="form-control" disabled>
                                    </div>
                                </div>
                                <div v-if="clienteInfo.letra == '0' || clienteInfo.letra == 'PE' || clienteInfo.letra == 'N' || clienteInfo.letra == 'PI' || clienteInfo.letra == 'E'">
                                    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                        <label>Provincia <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control" name="natural[provincia]" id="natural_provincia" disabled>
                                        <option value="">Seleccione</option>
                                        <option v-for="provincias in provinciasList" v-bind:value="provincias.valor" selected="{{provincias.id == clienteInfo.identificacion.split('-')[0]}}">
                                         {{{ provincias.valor}}}
                                        </option>                                 
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                        <label>Letras <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control" name="natural[letra]" id="natural[letra]" disabled>
                                            <option value="">Seleccione</option>
                                        <option v-for="letra in letrasList" v-bind:value="letra.valor" selected="{{letra.valor == clienteInfo.letra}}">
                                         {{{ letra.etiqueta}}}
                                        </option>  
                                        </select>
                                    </div>
                                    <div ng-show="naturalLetra.valor === null || naturalLetra.valor !== 'PAS'">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                            <label>Tomo <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="{{clienteInfo.identificacion.split('-')[1]}}" type="text" id="natural[tomo]" name="natural[tomo]" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                            <label>Asiento <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="{{clienteInfo.identificacion.split('-')[2]}}" type="text" id="natural[asiento]" name="natural[asiento]" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                            
                        
                        <!--/seccion de identificacion del cliente-->
                           
                        </div>
                        <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Teléfono </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input type="input-left-addon" name="campo[telefono]" v-model="clienteInfo.telefono" class="form-control" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]" disabled>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Correo Electrónico <span required="" aria-required="true">*</span></label><div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input type="input-left-addon" name="campo[correo]" v-model="clienteInfo.correo" data-rule-required="true" data-rule-email="true" class="form-control debito"  id="campo[correo]" disabled>
                                </div>
                                <label  for="campo[correo]" generated="true" class="error"></label>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Dirección" >
                                <label>Dirección</label>
                                <input type="text" name="campo[direccion]" v-model="clienteInfo.direccion" class="form-control" id="campo[direccion]" disabled>
                            </div>
                           <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 " style="margin-top: 5px;">
                               <br /><div class="input-group">

                                    <span class="input-group-addon"><input type="checkbox" v-model="exoneradoImpuestos" name="impuesto_checkbox" id="impuesto_checkbox" :checked="clienteInfo.exonerado_impuesto != NULL" /></span>
                                    <input type="text" value="Exonerado de impuestos" class="form-control" disabled/>
                                </div>
                                <label  for="campo[exonerado_impuesto]" generated="true" class="error"></label>
                            </div>
                          
                            
                        </div>

                        <div class="row">
                        <h5 style="font-size:14px">Plan</h5>
                        <hr style="margin-top:10px!important;"> 
                             <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 aseguradora "><label>Aseguradora  </label>
                                <select  name="campo[aseguradora_id]" class="form-control" id="aseguradoras" @change="nombrePlan()" :disabled="disabledAseguradora">
                                    <option value="">Seleccione</option>                                    
                                    <option v-for="aseg in aseguradorasListar" v-bind:value="aseg.id">
                                         {{aseg.nombre}}
                                        </option>                                    
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><label>Nombre del Plan  </label>
                                <select  name="campo[tipo_identificacion]" class="form-control" id="planes" :disabled="disabledOpcionPlanes" @change="coberturasPlan()">
                                    <option value="">Seleccione</option>
                                <option v-for="plan in planesInfo" v-bind:value="plan.id">{{plan.nombre}}</option>
                            </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 plan">
                                <br />
                                <button type="button" class="btn btn-default btn-block" v-on:click='coberturasModal()' id="ver_coberturas" style="width:100%;" :disabled="disabledCoberturas">Coberturas</button>
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Comisión % <span required="" aria-required="true">*</span></label><div class="input-group">
                                    <span class="input-group-addon">%</span>
                                    <input type="input-left-addon" name="campo[comision]" v-model="comisionPlanInfo" data-rule-required="true" data-rule-email="true" class="form-control"  id="campo[correo]" disabled>
                                </div>                                
                            </div>
                        </div>
                    <!-- Vigencia y detalle de solicitud -->    
                    <div class="row">
                        <h5 style="font-size:14px">Vigencia y detalle de solicitud</h5>
                        <hr style="margin-top:10px!important;">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                            <label for="">Vigencia</label>
                            <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                            <input type="input" id="vigencia_desde" readonly="readonly" class="form-control" value="">
                                    <span class="input-group-addon">a</span>
                                    <input type="input" id="vigencia_hasta" readonly="readonly" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Suma Asegurada </label><div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon" name="campo[suma_asegurada]" value="" data-rule-required="true" class="form-control"  id="suma_asegurada">
                                </div>                                
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan"><label>Pagador  </label>
                                <select  name="campo[pagador]" class="form-control" id="planes">
                                    <option value="">Seleccione</option>
                                <option v-for="pag in catalogoPagador" v-bind:value="pag.valor">{{pag.etiqueta}}</option>
                            </select>
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
                            <label>Póliza declarativa</label>
                        <div class="switch">
                             <input type="checkbox" class="js-switch" name='campo[poliza_declarativa]' id='polizaDeclarativa'/>
                            </div>       
                            </div>                        
                        </div>
                  <!-- Prima e informacion de cobros -->    
                    <div class="row">
                        <h5 style="font-size:14px">Prima e informaci&oacute;n de cobros</h5>
                        <hr style="margin-top:10px!important;">                             
                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                            <label>Prima Anual </label><div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="input-left-addon" name="campo[prima_anual]" v-model="primaAnual" class="form-control"  id="prima_anual">                       
                            </div>                          
                            </div>
                        <div class="form-group col-xs-6 col-sm-3 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                            <br /> +
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                            <label>Impuesto </label><div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon" name="campo[impuesto]" v-model="impuestoMonto" data-rule-required="true" data-rule-email="true" class="form-control"  id="impuesto" disabled>
                                </div>                                
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                            <br /> +
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                            <label>Otros </label><div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon" name="campo[otros]" v-model="otrosPrima" data-rule-required="true" data-rule-email="true" class="form-control"  id="otros">
                                </div>                                
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                            <br /> -
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                            <label>Descuentos </label><div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon" name="campo[descuentos]" v-model="descuentosPrima" data-rule-required="true" data-rule-email="true" class="form-control"  id="descuentos">
                                </div>                                
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                            <br /> =
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-3 ">
                            <label>Total </label><div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon" name="campo[total]" v-model="totalPrima" data-rule-required="true" data-rule-email="true" class="form-control"  id="total" disabled>
                                </div>                                
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><label>Cantidad de pagos  </label>
                                <select  name="campo[pagador]" class="form-control" id="planes">
                                    <option value="">Seleccione</option>
                                <option v-for="canpag in catalogoCantidadPagos" v-bind:value="canpag.valor">{{canpag.etiqueta}}</option>
                            </select>
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><label>Frecuencia de pagos  </label>
                                <select  name="campo[pagador]" class="form-control" id="planes">
                                    <option value="">Seleccione</option>
                                <option v-for="frecuencia in catalogoFrecuenciaPagos" v-bind:value="frecuencia.valor">{{frecuencia.etiqueta}}</option>
                            </select>
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><label>M&eacute;todo de pago  </label>
                                <select  name="campo[pagador]" class="form-control" id="planes">
                                    <option value="">Seleccione</option>
                                <option v-for="metodoPago in catalogoMetodoPago" v-bind:value="metodoPago.valor">{{{metodoPago.etiqueta}}}</option>
                            </select>
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><label>Sitio de pago  </label>
                                <select  name="campo[pagador]" class="form-control" id="planes">
                                    <option value="">Seleccione</option>
                                <option v-for="sitio in catalogoSitioPago" v-bind:value="sitio.valor">{{{sitio.etiqueta}}}</option>
                            </select>
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><label>Centro de facturaci&oacute;n  </label>
                                <select  name="campo[pagador]" class="form-control" id="centro_facturacion" :disabled="disabledCentro" @change="clienteDireccion()">
                                    <option value="">Seleccione</option>
                                <option v-for="centroFac in catalogoCentroFacturacion" v-bind:value="centroFac.id">{{{centroFac.nombre}}}</option>
                            </select>
                            </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><br />
                                <input name="campo[direccion]" type="text" v-model="clienteCentro" class="form-control" style="margin-top:5px;"/>
                            </div>
                        </div>
                  <!-- Distribucion de participacion -->    
                    <div class="row" id="participacion">
                        <h5 style="font-size:14px">Distribuci&oacute;n de participaci&oacute;n</h5>
                        <hr style="margin-top:10px!important;">
                        
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10" style="margin-left: -12px;">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                        <label>Agente</label>    
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <label>% Participaci&oacute;n</label>    
                        </div>
                        <div v-for="agt in agentesArray" track-by="$index">
                        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-4">
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-pie-chart"></i></span>
                        <select  name="agentes[]" class="form-control" id="agentes_{{$index}}" @change="porcentajeAgentes($index)">
                        <option value="">Seleccione</option>
                        <option v-for="agtList in agentesList" v-bind:value="agtList.id">{{{agtList.nombre +" "+ agtList.apellido}}}</option>
                        </select>
                        </div>    
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-4">
                        <input type="text" name="agentes_participacion[]" v-model="porcentajeParticipacion" class="form-control" id="agentes_participacion_{{$index}}">
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-1" style="margin-bottom: 5px;">
                        <button type="button" class="btn btn-default btn-block" @click="removeAgente(agt)"><i class="fa fa-trash"></i></button>
                        </div>                                                
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-1">
                        <button type="button" class="btn btn-default btn-block" @click="addAgente"><i class="fa fa-plus"></i></button>
                        </div>
                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
                        <label>Totales</label>    
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-4">
                        <input type="text" class="form-control" v-model="participacionTotal" />
                        </div>
                        </div>                        
                        </div>
                    </div></div>
            </div>           
            <div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('solicitudes/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> </div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                    <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit">
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>

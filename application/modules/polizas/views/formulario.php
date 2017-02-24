

<div id="datosdepoliza-5" class="tab-pane active col-lg-12 col-md-12">

    <input type="hidden" name="campo[uuid]" id="uuid" <?php if (isset($campos['uuid_polizas'])) { ?> value="<?php
    echo $campos['uuid_polizas'];
}
?>">
<input type="hidden" name="campo[id]" id="idPoliza" value="<?php echo $campos['id']; ?>">
<input type="hidden" name="campo[empresa_id]" id="empresa_id" <?php if (isset($campos['empresa_id'])) { ?>value="<?php
echo $campos['empresa_id'];
}
?>">
<input type="hidden" name="campo[usuario]" id="usuario" <?php if (isset($campos['usuario'])) { ?>value="<?php
echo $campos['usuario'];
}
?>">
<input type="hidden" name="campo[creado_por]" id="creado_por" <?php if (isset($campos['creado_por'])) { ?>value="<?php
echo $campos['creado_por'];
}
?>">

<style type="text/css">
    .sticky{ position: fixed !important; top: 0px; z-index: 100; background: #e7eaec;}
</style>

<div class="ibox"> 
    <div class="tabs-container">
        <ul class="nav nav-tabs tab-principal">
            <li class="active"><a data-toggle="tab" href="#tab-1" style="color:#337ab7!important;" onclick="location.href = '#datosGenerales'">Datos generales</a></li>
            <li class=""><a data-toggle="tab" href="#divplan" style="color:#337ab7!important;" onclick="location.href = '#divplan'">Plan</a></li>
            <li class=""><a data-toggle="tab" href="#divintereses" style="color:#337ab7!important;" onclick="location.href = '#divintereses'">Interés asegurado</a></li>
            <li class=""><a data-toggle="tab" href="#divvigencia" style="color:#337ab7!important;" onclick="location.href = '#divvigencia'">Vigencia</a></li>
            <li class=""><a data-toggle="tab" href="#divprima" style="color:#337ab7!important;" onclick="location.href = '#divprima'">Prima y cobros</a></li>
            <li class=""><a data-toggle="tab" href="#participacion" style="color:#337ab7!important;" onclick="location.href = '#divparticipacion'">Dist. de participación</a></li>
        </ul>
    </div>

    <div class="ibox-title" id="datosGenerales">
        <h5>Datos de Poliza</h5>
        <div class="ibox-content" style="display: block;" >
            <div class="row renewal">
               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                   <label>N° Póliza</label>
                   <input type="text" class="form-control" name="numeroPoliza" v-model="numeroPoliza">
               </div>
           </div>
           <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3"><label>Nombre del cliente <span required="" aria-required="true">*</span></label>
            <input type="text" name="poliza_cliente_nombre" value="{{polizaCliente.nombre_cliente}}" class="form-control ncli" id="poliza_cliente_nombre" disabled />
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3"><label>Identificación  </label>
            <input type="text" name="poliza_cliente_tipo_identificacion" class="form-control" id="poliza_cliente_tipo_identificacion" value="{{polizaCliente.identificacion}}" disabled />
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>N° Identificación <span required="" aria-required="true">*</span></label>
            <input type="text" id="numeroIdentificacion" value="{{polizaCliente.n_identificacion}}" id="poliza_cliente_n_identificacion" class="form-control" disabled />
        </div> 
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Grupo <span required="" aria-required="true">*</span></label>
            <input type="text" value="{{polizaCliente.grupo}}" id="poliza_cliente_grupo" name="poliza_cliente_grupo" class="form-control" disabled />
        </div> 
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
            <label>Teléfono </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input type="text" value="{{polizaCliente.telefono}}" name="poliza_cliente_telefono"  class="form-control" id="poliza_cliente_telefono" disabled />
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
            <label>Correo electrónico <span required="" aria-required="true">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">@</span>
                <input type="input-left-addon" value="{{polizaCliente.correo_electronico}}" name="poliza_cliente_correo" class="form-control debito"  id="poliza_cliente_correo" disabled />
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
            <label>Dirección</label>
            <input type="text" value="{{polizaCliente.direccion}}" name="poliza_cliente_direccion" class="form-control" id="poliza_cliente_direccion" disabled /> 
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
            <label>Exonerado de impuesto</label>
            <br/>
            <div class="input-group">
                <span class="input-group-addon"><input type="checkbox"  name="impuesto_checkbox" id="impuesto_checkbox" :checked="polizaCliente.exonerado_impuesto != '' " disabled/></span>
                <input type="text" value="{{polizaCliente.exonerado_impuesto}}" placeholder="Exonerado de impuesto" class="form-control" disabled/><div id="divplan"></div>
            </div>
        </div>


    </div> 


    <h5>Plan</h5> 
    <div class="ibox-content" style="display: block;" >
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 aseguradora "><label>Aseguradora  </label>
                <input type="input-left-addon" name="poliza_aseguradora_id" class="form-control" id="poliza_aseguradora" value="{{polizaAseguradora[0].nombre}}" disabled />
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan"><label>Nombre del Plan  </label>
                <input type="input-left-addon" name="poliza_plan_id" class="form-control" id="poliza_plan" value="{{polizaPlan[0].nombre}}" disabled />
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 plan">
                <br/>
                <button type="button" class="btn btn-default btn-block" id="poliza_coberturas" style="width:100%;" v-on:click='coberturasModal()'>Coberturas</button>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                <label>Comisión % <span required="" aria-required="true">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">%</span>
                    <input type="input-left-addon" name="poliza_comision" v-model="comision" class="form-control"  id="poliza_comision" value="{{polizaComision}}" @change="prueba()" :disabled="!disabledComision" /><div id="divintereses">
                    <input type="hidden" name="idPolicy" v-model="idPolicy">
                </div>  
            </div> 
        </div>
    </div>



    <?php
    if (!isset($campo)) {
        $campo = array();
    }
    ?>

    <?php echo modules::run('polizas/formulariointereses', $campos); ?>  


    <h5>Vigencia y detalle de solicitud</h5>
    <div class="ibox-content" style="display: block;">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-4">
                <label for="">Vigencia<span required="" aria-required="true">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                    <input type="input-left-addon" id="poliza_vigencia_desde" name="poliza_vigencia_desde" class="form-control datepicker" value="{{polizaVigencia.vigencia_desde}}" :disabled="disabledfechaInicio"  v-model="fechaInicio" required />
                    <span class="input-group-addon">a</span>
                    <input type="input-left-addon" id="poliza_vigencia_hasta" name="poliza_vigencia_hasta" class="form-control datepicker2" value="{{polizaVigencia.vigencia_hasta}}" :disabled="disabledfechaExpiracion" v-model="fechaExpiracion" required />
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label>Suma Asegurada <span required="" aria-required="true">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="input-left-addon" name="poliza_suma_asegurada" class="form-control"  id="poliza_suma_asegurada" value="{{polizaVigencia.suma_asegurada}}" disabled />
                </div>                                
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label>Pagador  <span required="" aria-required="true">*</span></label>
                <input type="input-left-addon" name="poliza_tipo_pagador" class="form-control" id="poliza_pagador" value="{{polizaVigencia.tipo_pagador}}" disabled/>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2">
                <label>Nombre  <span required="" aria-required="true">*</span></label>
                <input type="input-left-addon" name="poliza_pagadornombre" id="poliza_campopagador" class="form-control" value="{{polizaVigencia.pagador}}" disabled/>  
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 poliza_declarativa">
                <label>Póliza declarativa</label>
                <div id="divprima"></div><input type="checkbox" class="js-switch" name='poliza_declarativa' id='polizaDeclarativa' <?php if($campos['poliza_declarativa'] == "si"){echo "checked";} ?> disabled />
            </div>
        </div>   
    </div>
     <?php
            //print_r($campos);
    if (empty($campos))
        $campos = "";
    $formAttr = array(
        'method' => 'POST',
        'id' => 'formPolizasCrear',
        'autocomplete' => 'off'
        );
    if (isset($campos['uuid_polizas']) && ($campos['uuid_polizas'] != "")) {
        echo form_open(base_url('polizas/editar/' . $campos['uuid_polizas']), $formAttr);
    } else {
        echo form_open(base_url('polizas/guardar'), $formAttr);
    }
    ?>

    <h5>Prima e informaci&oacute;n de cobros</h5>
    <div class="ibox-content" style="display: block;" >
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                <label>Prima Anual <span required="" aria-required="true">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="input-left-addon" name="poliza_prima_anual" class="form-control"  id="poliza_prima_anual" value="{{polizaPrima.prima_anual}}" :disabled="cambiarOpcionesPago" />
                </div>                          
            </div>
            <div class="form-group col-xs-6 col-sm-3 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                <br /> -
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                <label>Descuentos </label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="input-left-addon" name="poliza_descuentos" class="form-control"  id="poliza_descuentos" value="{{polizaPrima.descuentos}}" :disabled="cambiarOpcionesPago" />
                </div>                                
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                <br /> +
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                <label>Otros </label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="input-left-addon" name="poliza_otros" class="form-control" id="poliza_otros" value="{{polizaPrima.otros}}" :disabled="cambiarOpcionesPago" />
                </div>                                
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                <br /> +
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                <label>Impuesto <span required="" aria-required="true">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="input-left-addon" name="poliza_impuesto" class="form-control" id="poliza_impuesto" value="{{polizaPrima.impuesto}}" :disabled="cambiarOpcionesPago" />
                </div>                                
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1 " style="text-align: center; margin-top: 12px; width: 20px;">
                <br /> =
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
                <label>Total </label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="input-left-addon" name="poliza_total" class="form-control"  id="poliza_total" value="{{polizaPrima.total}}" :disabled="cambiarOpcionesPago" />
                </div>                                
            </div>
        </div> 
    
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
                <label>Frecuencia de pagos <span required="" aria-required="true">*</span> </label>
                <select  name="campoprima[frecuencia_pago]" class="form-control" id="frecuenciapagos" data-rule-required="true" :disabled="cambiarOpcionesPago">
                    <option value="">Seleccione</option>
                    <option v-for="frecuencia in catalogoFrecuenciaPagos" v-bind:value="frecuencia.valor" :selected="frecuencia.valor == polizaPrima.frecuencia_pago">{{frecuencia.etiqueta}}</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 plan">
                <label>M&eacute;todo de pago <span required="" aria-required="true">*</span> </label>
                <select  name="campoprima[metodo_pago]" class="form-control" id="metodopago" data-rule-required="true" :disabled="cambiarOpcionesPago">
                    <option value="">Seleccione</option>
                    <option v-for="metodoPago in catalogoMetodoPago" v-bind:value="metodoPago.valor" :selected="metodoPago.valor == polizaPrima.metodo_pago" >{{{metodoPago.etiqueta}}}</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-4 col-md-3 col-lg-3">
                <label>Fecha primer Pago <span required="" aria-required="true">*</span> </label>
                <div class="input-group" >
                    <span class="input-group-addon"><i class="fa fa-calendar "></i></span>    
                    <input type="input" id="fecha_primer_pago" name="campoprima[fecha_primer_pago]"  class="form-control date datepicker2" value="{{polizaPrima.fecha_primer_pago}} " data-rule-required="true" :disabled="cambiarOpcionesPago">
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2 plan">
                <label>Cantidad de pagos <span required="" aria-required="true">*</span> </label>
                <select  name="campoprima[cantidad_pagos]" class="form-control" id="cantidadpagos" data-rule-required="true" :disabled="cambiarOpcionesPago">
                    <option value="">Seleccione</option>
                    <option v-for="canpag in catalogoCantidadPagos" v-bind:value="canpag.valor" :selected="canpag.etiqueta == polizaPrima.cantidad_pagos">{{canpag.etiqueta}}</option>
                </select>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
                <label>Sitio de pago <span required="" aria-required="true">*</span> </label>
                <select  name="campoprima[sitio_pago]" class="form-control" id="sitiopago" data-rule-required="true" :disabled="cambiarOpcionesPago">
                    <option value="">Seleccione</option>
                    <option v-for="sitio in catalogoSitioPago" v-bind:value="sitio.valor" :selected="sitio.valor == polizaPrima.sitio_pago">{{{sitio.etiqueta}}}</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
                <label>Centro de facturaci&oacute;n  </label>
                <select  name="campoprima[centro_facturacion]" class="form-control" id="centro_facturacion" @change="getClienteDireccion()" data-rule-required="true" :disabled="cambiarOpcionesPago">
                    <option value="">Seleccione</option>
                    <option v-for="centroFac in catalogoCentroFacturacion" v-bind:value="centroFac.id" :selected="centroFac.id == polizaPrima.centro_facturacion">{{{centroFac.nombre}}}</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">  
                <label for="direccion">Dirección</label>
                <div id="participacion"></div><input name="campoprima[direccion_pago]" type="text" v-model="clienteCentro" class="form-control" value="{{polizaPrima.direccion_pago}}" :disabled="cambiarOpcionesPago"/>
            </div>
        </div>
    <h5 style="font-size:14px">Distribuci&oacute;n de participaci&oacute;n</h5>
    <div class="ibox-content" style="display: block;" >
        <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="margin-left: -12px;">

                <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label>Agente</label>    
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label>% Participaci&oacute;n</label>    
                        </div>
                        <div v-for="par in polizaParticipacion"  id="total_agentes_participantes" class="total_agentes_participantes" track-by="$index">
                            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pie-chart"></i></span>
                                    <select type="text"  name="agente[]" class="form-control id_agentes" id="poliza_agente" value="{{par.agente}}" :disabled="disabledAgente" >
                                        <option v-model="par.agente">{{par.agente}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                                <input type="text" name="participacion[]" v-model="addons[$index]" class="form-control moneda" id="poliza_agentes_participacion"  value="{{par.porcentaje_participacion != '' ? par.porcentaje_participacion : ''}}"  :disabled="disableParticipacion" @keyup="total()">
                            </div>
                        </div>

                    </div>
				<br>
				<div class="row agentePrincipal ">
					<div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
						<div class="input-group">	
							<span class="input-group-addon"><i class="fa fa-pie-chart"></i></span>
							<select id='nombreAgentePrincipal' class="form-control" disabled>
							</select>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
						 <input type="form-control moneda" class="form-control" id="porcAgentePrincipal" disabled style="text-align: right;">  
					</div><br>
				</div>
				<br>
                <div class="row">
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-6">
                        <label>Totales</label>    
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-6 col-lg-6">
                        <input type="text" class="form-control moneda" id="poliza_participacion_total" value="{{polizaTotalParticipacion}}"
                        v-model="polizaTotalParticipacion" disabled/>
                    </div>
                </div>

            </div>
        </div>
    </div>
   

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
            <label>Estado</label>
            <div class="input-group">            
                <select  name="campo[estado]" class="form-control" id="estado_poliza" :disabled="disabledEstadoPoliza">
                    <?php
                    $por_facturar = "";
                    $facturada = "";
                    if (isset($campos['estado']) && $campos['estado'] == "Por Facturar") {
                        $por_facturar = "selected";
                    } else if (isset($campos['estado']) && $campos['estado'] == "Facturada") {
                        $facturada = "selected";
                    }
                    if ($campos['politicas_general'] > 0 && isset($campos['estado'])) {
                        if ($campos['estado'] == "Por Facturar") {

                            if (in_array(23, $campos['politicas']) && $campos['validar_politicas'] == 23) {
                                ?>
                                <option v-for="estado in comboEstado" v-bind:value="estado.valor" :selected="estado.valor==estado_pol">{{{estado.valor}}}</option>
                                <?php
                            } else if ($campos['validar_politicas'] == 23) {
                                ?>
                                <option value='Por Facturar' <?php echo $por_facturar ?> >Por Facturar</option>
                                <?php
                            }
                        } else {
                            ?>
                            <option v-for="estado in comboEstado" v-bind:value="estado.valor" :selected="estado.valor==estado_pol">{{{estado.valor}}}</option>
                            <?php
                        }
                    } else {
                        ?>
                        <option v-for="estado in comboEstado" v-bind:value="estado.valor" :selected="estado.valor==estado_pol">{{{estado.valor}}}</option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7"></div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-2" style="margin-left: -20px;">
                <label>Centro Contable</label>
                <input type="text" name="nombre_centroContable" id="centro_contable" class="form-control" value="{{nombre_centroContable}}" disabled>
            </div>
        </div>
    </div>
</div>  
<br><br>
<div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
<?php
        /* if($_GET['reg']=="ase")
          $url='aseguradoras/editar/'.$_GET['val'];
          else if($_GET['reg']=="age")
          $url='agentes/ver/'.$_GET['val'];
          else */
            $url = 'polizas/listar';

        ?>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><!--<a href="<?php echo base_url($url); ?>" class="btn btn-default btn-block cancelar" id="cancelar">Cancelar </a>--><a href="" onclick="window.history.back();" class="btn btn-default btn-block cancelar" id="cancelar">Cancelar </a> </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <?php
            if (isset($campos['uuid_polizas']) && ($campos['uuid_polizas'] != "")) {
                ?>
                <input type='hidden' name='campo[regreso]' value='<?php
                if (isset($_GET['reg']))
                    echo $_GET['reg'];
                else
                    ""
                ?>' />
                <input type='hidden' name='campo[regreso_valor]' value='<?php
                if (isset($_GET['val']))
                    echo $_GET['val'];
                else
                    ""
                ?>' />
                <?php
            }
            ?>
            <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block detail" id="guardar_poliza" > 
            <!-- :disabled="disabledSubmit" -->
            <button  name="campo[renovar]" @click="sendRenewalData()" id="renovar" class="btn btn-primary btn-block renewal">Renovar</button>
            <button  name="guardar_endoso"  id="endoso_guardar" class="btn btn-primary btn-block detail_endoso">Guardar</button>
        </div>
    </div>
</div>

<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div_coberturas" style="display:none;">

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Coberturas</label>    
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Valor</label>    
    </div>

    <div v-for="find in polizaCoberturas" track-by="$index">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5">
            <input type="text" name="coberturasNombre[]"  v-model="find.cobertura" class="form-control coberturas" disabled>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5">
            <input type="text" name="coberturasValor[]"  v-model="find.valor_cobertura" class="form-control coberturas moneda" disabled>
        </div>
    </div>
    <br>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Deducible</label>    
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Valor</label>    
    </div>
    <br>
    <div v-for="find in polizaDeducciones" track-by="$index">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-5">
            <input type="text" name="deduciblesNombre[]" v-model="find.deduccion" class="form-control coberturas" disabled>
        </div>
        <br>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-5">
            <input type="text" name="deduciblesValor[]" v-model="find.valor_deduccion" class="form-control coberturas moneda" disabled>
        </div>
    </div>
</div>
<?php
echo Modal::config(array(
    "id" => "verCoberturas",
    "size" => "lg"
    ))->html();

echo Modal::config(array(
    "id" => "opcionesModalIntereses",
    "size" => "sm"
    ))->html();
    ?>
<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formClienteCrear',
    'autocomplete' => 'off'
);

?>
<style type="text/css">
    .sticky{ position: fixed !important; top: 0px; z-index: 100; background: #e7eaec;}
</style>
<div id="vistaCliente" class="" >

    <div class="tab-content" >
        <!-- AQUI VA EL FORM CON EL ACTION -->
        <div id="datosdelcliente-5" class="tab-pane active col-lg-12 col-md-12">

            <div class="ibox" >                    
                <div class="tabs-container">
                    <ul class="nav nav-tabs tab-principal">
                        <li class="active"><a data-toggle="tab" href="#tab-1" style="color:#337ab7!important;" onclick="location.href = '#datosGenerales'">Datos generales</a></li>
                        <li class=""><a data-toggle="tab" href="#divplan" style="color:#337ab7!important;" onclick="location.href = '#divplan'">Plan</a></li>
                        <li class=""><a data-toggle="tab" href="#divintereses" style="color:#337ab7!important;" onclick="location.href = '#divintereses'">Interés asegurado</a></li>
                        <li class=""><a data-toggle="tab" href="#divvigencia" style="color:#337ab7!important;" onclick="location.href = '#divvigencia'">Vigencia</a></li>
                        <li class=""><a data-toggle="tab" href="#divprima" style="color:#337ab7!important;" onclick="location.href = '#divprima'">Prima y cobros</a></li>
                        <li class=""><a data-toggle="tab" href="#participacion" style="color:#337ab7!important;" onclick="location.href = '#participacion'">Dist. de participación</a></li>
                        <li class=""><a data-toggle="tab" href="#doc_tramites" style="color:#337ab7!important;" onclick="location.href = '#doc_tramites'">Documentos para trámites</a></li>
                    </ul>
                </div>

                <div class="ibox-content" style="display: block;" id="datosGenerales">
                    <div class="row">
                        <h5 style="font-size:14px">Datos generales</h5>
                        <hr style="margin-top:10px!important;"> 
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 NombredelCliente "><label>Nombre del cliente <span required="" aria-required="true">*</span></label>
                            <input type="text" name="campo[nombre]" value="{{clienteInfo.nombre}}" class="form-control ncli" id="campo[nombre]" data-rule-required="true" disabled>


                        </div>
                        <!--seccion de identificacion del cliente-->      
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Identificacíon "><label>Identificación  </label>
                            <select  name="campo[tipo_identificacion]" class="form-control" id="tipo_identificacion" disabled>
                                <option value="">Seleccione</option>                                    
                                <option value="{{clienteInfo.tipo_identificacion}}" selected>{{clienteInfo.tipo_identificacion}}</option>
                            </select>
                        </div> 
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label>N° Identificación <span required="" aria-required="true">*</span></label>
                            <input  value="{{clienteInfo.identificacion}}" type="text" id="numeroIdentificacion" class="form-control" disabled>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label>Grupo <span required="" aria-required="true">*</span></label>
                            <select name="natural[grupo]" data-rule-required="true" class="form-control grupo_sel" id="grupo_nombre" @change="grupoInfo()">
                                <!--<option value="{{clienteInfo.group.nombre}}" v-for="grupo in clienteInfo.group">{{clienteInfo.group.nombre}}</option>-->
                                <option v-for="grupo in clienteInfo.group"  v-bind:value="grupo.nombre"  selected="{{grupo.nombre == grupogbd}}" >
                                    {{grupo.nombre}}
                                </option>
                            </select>
                        </div>
                        <!--                             <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                                        <label>Grupo <span required="" aria-required="true">*</span></label>
                                                        <input  value="{{clienteInfo.group.nombre}}" type="text" id="natural[grupo]" name="natural[grupo]" class="form-control" disabled>
                                                    </div>             -->
                        <!--div tipo == pasaporte-->
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" v-if="clienteInfo.letra == 'PAS'">
                            <label>Pasaporte</label>
                            <input data-rule-required="true" type="text" name="pasaporte[pasaporte]" id="pasaporte[pasaporte]" value="{{clienteInfo.identificacion}}" class="form-control" disabled>
                        </div>

                        <!-- div oculto para enseñar campos juridico natural -->
                        <div v-if="clienteInfo.tipo_identificacion == 'juridico'">
                            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                <label>Tomo/Rollo <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" type="text" name="juridico[tomo]" id="juridico[tomo]" value="{{clienteInfo.identificacion.split('-')[0]}}"  class="form-control" disabled>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                <label>Folio/Imagen/Documento <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" type="text" name="juridico[folio]" value="{{clienteInfo.identificacion.split('-')[1]}}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                <label>Asiento/Ficha <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" type="text" value="{{clienteInfo.identificacion.split('-')[2]}}" id="juridico[asiento]" class="form-control" disabled>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                <label>Digito verificador <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" type="text" value="{{clienteInfo.identificacion.split('-')[3]}}" name="juridico[verificador]" id="juridico[verificador]" class="form-control" disabled>
                            </div>
                        </div>
                        <div v-if="clienteInfo.letra=='not-show'">
                            <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Provincia <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control" name="natural[provincia]" id="natural_provincia" disabled>
                                    <option value="">Seleccione</option>
                                    <option v-for="provincias in provinciasList" v-bind:value="provincias.id" selected="{{provincias.id == clienteInfo.identificacion.charAt(0)}}">
                                        {{ provincias.valor}}
                                    </option>                                 
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Letras <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control" name="natural[letra]" id="natural[letra]" disabled>
                                    <option value="">Seleccione</option>
                                    <option v-for="letra in letrasList" v-bind:value="letra.valor" selected="{{letra.valor == clienteInfo.letra}}">
                                        {{letra.etiqueta}}
                                    </option>  
                                </select>
                            </div>
                            <div ng-show="naturalLetra.valor === null || naturalLetra.valor !== 'PAS'">
                                <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                    <label>Tomo <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="{{clienteInfo.identificacion.split('-')[1]}}" type="text" id="natural[tomo]" name="natural[tomo]" class="form-control" disabled>
                                </div>
                                <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
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
                            <label>Correo electrónico <span required="" aria-required="true">*</span></label><div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="input-left-addon" name="campo[correo]" v-model="clienteInfo.correo" data-rule-required="true" data-rule-email="true" class="form-control debito"  id="campo[correo]" disabled>
                            </div>
                            <label  for="campo[correo]" generated="true" class="error"></label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Dirección" >
                            <label>Dirección</label>
                            <!-- <input type="text" name="campo[direccion]" v-model="clienteInfo.direccion" class="form-control" id="campo[direccion]" disabled> -->
                            <select class="form-control" name="natural[direccion]" class="form-control grupo_sel" id="direccion_nombre" @change="direccionInfo()">
                                <option v-bind:value="{{address.direccion}}" v-for="address in clienteInfo.direccion" selected="{{address.direccion == direcciongbd}}">{{address.direccion}}</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 " style="margin-top: 5px;">
                            <label>Exonerado de impuesto</label>
                            <br /><div class="input-group">

                                <span class="input-group-addon"><input type="checkbox" v-model="exoneradoImpuestos" name="impuesto_checkbox" id="impuesto_checkbox" /></span>
                                <input type="text" value="{{clienteInfo.exonerado_impuesto}}" placeholder="Exonerado de impuesto" class="form-control" :disabled="!exoneradoImpuestos"/>
                            </div>
                            <label  for="campo[exonerado_impuesto]" generated="true" class="error"></label>
                        </div>
                        <div id="divplan">
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
                            <select  name="campo[plan_id]" class="form-control" id="planes" :disabled="disabledOpcionPlanes" @change="coberturasPlan()">
                                <option value="">Seleccione</option>
                                <option v-for="plan in planesInfo" v-bind:value="plan.id">{{plan.nombre}}</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 plan">
                            <br />
                            <button type="button" class="btn btn-default btn-block" v-on:click='coberturasModal()' id="ver_coberturas" style="width:100%;" :disabled="disabledCoberturas">Coberturas</button>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 "> 
                            <label>Comisión % <span required="" aria-required="true">*</span></label><div id="divintereses"></div>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input type="input-left-addon" name="campo[comision]" v-model="comisionPlanInfo" data-rule-required="true" data-rule-email="true" class="form-control comision_solicitud"  id="campo[comision]" :disabled="isEditable">
                            </div>                                
                        </div>

                    </div>

                    <!-- Intereses de solicitud -->    
                    <div class="row">

                        <?php
                        if (!isset($campo)) {
                            $campo = array();
                        }
                        ?>

                        <?php echo modules::run('solicitudes/formulariointereses', $campo); ?>  

                    </div> 

                    <?php echo form_open_multipart(base_url('solicitudes/guardar'), $formAttr); ?>

                    <input name="campo[cliente_id]" type="hidden" value="{{clienteInfo.id}}" />
                    <input name="campo[grupo]" type="hidden" id="grupoInfo" />
                    <input name="campo[direccion]" type="hidden" id="direccionInfo" />
                    <input name="campo[nombre_cliente]" type="hidden" value="{{clienteInfo.nombre}}" />
                    <input name="campo[ramo]" id="nombre_ramo" type="hidden" v-model="ramo" />
                    <input name="campo[id_tipo_poliza]" type="hidden" v-model="tipoPoliza" />
                    <input name="codigo_ramo" type="hidden" v-model="codigoRamo" />
                    <input id="nombre_padre" type="hidden" v-model="nombrepadre" />

                    <input type="hidden" id="idSolicitud" name="id_solicitud" value="">
                    <input type="hidden" id="ramo_id" name="campo[ramo_id]" value="">
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" value="">
                    <input type="hidden" name="campo[id_solicitud]" id="campo[id_solicitud]" value="">

                    <input type="hidden" name="campoPlanesCoberturas[planesCoberturasDeducibles]" id="planesCoberturasDeducibles">
                    <input type="hidden" name="campo[comision]" id="comision">
                    <input type="hidden" name="campo[porcentaje_sobre_comision]" id="porcentaje_sobre_comision" value="{{sobreComision}}">

                    <input type="hidden" name="campo[aseguradora_id]" id="aseguradoras2">
                    <input type="hidden"  name="campo[plan_id]" id="planes2">               

                    <input type="hidden" name="detalleunico" id="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                    <input type="hidden" name="reg" id="reg" value="<?php echo !empty($_GET['reg']) ? $_GET['reg'] : ''; ?>"> 
                    <input type="hidden" name="val" id="val" value="<?php echo !empty($_GET['val']) ? $_GET['val'] : ''; ?>"> 

                    <!-- Vigencia y detalle de solicitud -->    
                    <div class="row">
                        <?php
                        if (!isset($campo)) {
                            $campo = array();
                        }
                        ?>
                        <?php echo modules::run('solicitudes/formulariovigencia', $campo); ?>

                    </div>
                    <!-- Prima e informacion de cobros -->    
                    <div class="row" >
                        <?php echo modules::run('solicitudes/formularioprima', $campo); ?>
                    </div>
                    <!-- Distribucion de participacion -->  
                    <div class="row">
                        <?php echo modules::run('solicitudes/formularioparticipacion', $campo); ?>

                    </div>
                      <!-- Documentos -->  
                      <div class="row" id="doc_tramites">

                        <?php
                        echo modules::run('solicitudes/formulariodocumentos', $campo); ?>

                    </div>
                </div></div>
        </div>           
        <?php
            $url = 'solicitudes/listar';
            if(!empty($_GET['val']) && $_GET['reg'] == "age"){
                $url = 'agentes/ver/'.$_GET['val'];
            }
            else if(!empty($_GET['val']) && $_GET['reg'] == "aseg"){
                $url = 'aseguradoras/editar/'.$_GET['val'];
            }
        ?>
        <div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url($url); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> </div>

            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 guardarsolicitud">
                <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit">
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php
echo Modal::config(array(
    "id" => "opcionesModalIntereses",
    "size" => "sm"
))->html();

echo Modal::config(array(
    "id" => "documentosModal",
    "size" => "lg",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("documentos/formulario", array())
))->html();

echo Modal::config(array(
    "id" => "AprobarSolicitud",
    "size" => "lg",
))->html();

echo Modal::config(array(
    "id" => "AnularSolicitud",
    "size" => "lg",
))->html();

echo Modal::config(array(
    "id" => "RechazarSolicitud",
    "size" => "lg",
))->html();
?>

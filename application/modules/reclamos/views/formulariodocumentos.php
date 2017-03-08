<h5 style="font-size:14px">Documentos para tr&aacute;mites</h5>
<hr style="margin-top:10px!important;">
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="documentos_todos">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?php  ?>
                    <?php if (isset($campos['documentacion']) && count($campos['documentacion']) > 0) { ?>
                        <?php
                        $i = 0;
                        foreach ($campos['documentacion'] AS $i => $doc) {
                            $obligatorio = $doc->categoria;
                            if ($obligatorio == "Obligatorio") {
                                $requerido = "Si";
                            } else {
                                $requerido = "No";
                            }
                            ?>
                            <div id="campo_documentacion" class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="vertical-align: middle;">
                                <input <?php if ($doc->categoria == "Obligatorio") { ?> data-rule-required="true" <?php } ?> id="documentacion_<?php print $i; ?>" type="checkbox" value="<?php print $doc->nombre; ?>" v-model="documentacion" @click="documenteshion(<?php print $i; ?>,'<?php echo $doc->nombre; ?>','<?php echo $requerido; ?>','')" name="documentacion[]_<?php print $i; ?>" /> <label class="label_espacio documentaciones_<?php print $i; ?>">

                                    <?php
                                    print $doc->nombre;
                                    if ($doc->categoria == "Obligatorio") {
                                        ?><span required="" aria-required="true">*</span><?php } ?></label>
                                <span id="error_check"></span>
                            </div>
                        <?php 
                        } 
                        ?>
                        <input type="hidden" value="reclamo" id="modulo" name="campodocumentacion[modulo]" class="modulo">
                        <input type="hidden" value="{{documentacion}}" id="opcion" name="campodocumentacion[opcion]" class="opcion">
                        <input type="hidden" value="<?php echo $i; ?>" id="cantidad_check" name="campodocumentacion[cantidad_check]" class="cantidad_check">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <label class="nombre_doc_titulo" id="nombre_doc_titulo">Nombre del documento</label>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <?php for ($j = 0; $j <= $i; $j++) { ?>

                                <div id='file_tools_solicitudes_<?php echo $j ?>'>
                                </div>

                            <?php } ?>
                        </div>


                    <?php } ?>
                </div>
            </div>

            <div class="docentregados_crear row" id="docentregados_crear">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <label class="nombre_doc_titulo" id="nombre_doc_titulo">Nombre del documento</label>
                </div>
                <div style="margin-bottom: 25px;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div class='file_upload_solicitudes_adicionales' id='h1'>
                        <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" />
                        <input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                        <input type="hidden" value="new" name="campotipodoc[]">
                        <br><br>
                    </div>
                    <div id='file_tools_solicitudes_adicionales'>
                        <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_solicitudes_adicionales"><i class="fa fa-plus"></i>
                        </button>
                        <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_solicitudes_adicionales"><i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row documentos_editar" id="documentos_editar">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 25px;">                
                    <div class="row">
                        <div v-for="doc in documentacionesList" class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="vertical-align: middle;">
                            <label v-if='doc.categoria == "Obligatorio"'>
                            <input id="documentacion_{{$index}}" style="margin-left: 5px!important;" type="checkbox" v-bind:value="doc.nombre" v-model="documentacion" @click="documenteshion($index,doc.nombre,'reclamo','1')" v-bind:data-valor="documentacionesgbd[$index].valor" v-bind:data-nombre="doc.nombre" name="documentacion_{{$index}}"  v-bind:checked="doc.nombre==arrayauxiliar[doc.nombre]" data-rule-required="true"/>
                            </label>
                            <label v-if='doc.categoria != "Obligatorio"'>
                            <input id="documentacion_{{$index}}" style="margin-left: 5px!important;" type="checkbox" v-bind:value="doc.nombre" v-model="documentacion" @click="documenteshion($index,doc.nombre,'reclamo','1')" v-bind:data-valor="documentacionesgbd[$index].valor" v-bind:data-nombre="doc.nombre" name="documentacion_{{$index}}"  v-bind:checked="doc.nombre==arrayauxiliar[doc.nombre]"/>
                            </label>
                            <label style="margin-left: 5px!important;" class="label_espacio">{{doc.nombre}}</label>
                            <span v-if="doc.categoria=='Obligatorio'" required="" aria-required="true">*</span>
                            <span id="error_check"></span>
                        </div>
                        <div class="row"></div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <label style="margin-bottom: 15px; margin-top: 20px;" class="nombre_doc_titulo_editar" id="nombre_doc_titulo_editar">Nombre del documento</label>
                                <div v-for="docum in documentacionesList">

                                    <div id='file_tools_solicitudes_{{$index}}'>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="cantidad_check" name="campodocumentacion[cantidad_check]" class="cantidad_check">
                            <input type="hidden" value="{{documentacion}}" id="opcion" name="campodocumentacion[opcion]" class="opcion">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="documentos_vehiculo">
            <!-- Comienzo TabPanel -->
            <div role="tabpanel">
                <!-- Tab panes -->
                <div class="row tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabla">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="">
                                    <ul class="nav nav-tabs">
                                        <li class="active" id="tab_colision"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="colision">Colisión/Vuelco</a></li>
                                        <li class="" id="tab_asistencia"><a data-toggle="tab" href="#tab-2" aria-expanded="false"  data-targe="asistencia">Asistencia Legal</a></li>
                                        <li class="" id="tab_terceros"><a data-toggle="tab" href="#tab-3" aria-expanded="false"  data-targe="terceros">Daños Terceros</a></li>
                                        <li class="" id="tab_comprensivo"><a data-toggle="tab" href="#tab-4" aria-expanded="false"  data-targe="comprensivo">Comprensivo</a></li>
                                        <li class="" id="tab_medicos"><a data-toggle="tab" href="#tab-5" aria-expanded="false"  data-targe="medicos">Gastos médicos</a></li>
                                        <li class="" id="tab_recobro"><a data-toggle="tab" href="#tab-6" aria-expanded="false"  data-targe="recobro">Recobro</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane active">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                        <div class=" col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_0" type="checkbox" value="Aviso Operaciones" v-model="documentacion" @change="documenteshion(0,'Aviso Operaciones','No','')" name="documentacion[]_0" v-bind:checked="existeDocumento('Aviso Operaciones') == true " /> 
                                                                <label class="label_espacio documentaciones_0">Aviso Operaciones</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_1" type="checkbox" value="Carta de responsabilidad" v-model="documentacion" @change="documenteshion(1,'Carta de responsabilidad','No','')" name="documentacion[]_1" v-bind:checked="existeDocumento('Carta de responsabilidad') == true " /> 
                                                                <label class="label_espacio documentaciones_1">Carta de responsabilidad</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_2" type="checkbox" value="Cédula" v-model="documentacion" @change="documenteshion(2,'Cédula','No','')" name="documentacion[]_2" v-bind:checked="existeDocumento('Cédula') == true " /> 
                                                                <label class="label_espacio documentaciones_2">Cédula</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_3" type="checkbox" value="Cotización" v-model="documentacion" @change="documenteshion(3,'Cotización','No','')" name="documentacion[]_3" v-bind:checked="existeDocumento('Cotización') == true " /> 
                                                                <label class="label_espacio documentaciones_3">Cotización</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div class=" col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_4" type="checkbox" value="Elección de taller" v-model="documentacion" @change="documenteshion(4,'Elección de taller','No','')" name="documentacion[]_4" v-bind:checked="existeDocumento('Elección de taller') == true " /> 
                                                                <label class="label_espacio documentaciones_4">Elección de taller</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_5" type="checkbox" value="Factura original" v-model="documentacion" @change="documenteshion(5,'Factura original','No','')" name="documentacion[]_5" v-bind:checked="existeDocumento('Factura original') == true " /> 
                                                                <label class="label_espacio documentaciones_5">Factura original</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_6" type="checkbox" value="Fotos" v-model="documentacion" @change="documenteshion(6,'Fotos','No','')" name="documentacion[]_6" v-bind:checked="existeDocumento('Fotos') == true " /> 
                                                                <label class="label_espacio documentaciones_6">Fotos</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_7" type="checkbox" value="FUD" v-model="documentacion" @change="documenteshion(7,'FUD','No','')" name="documentacion[]_7" v-bind:checked="existeDocumento('FUD') == true " /> 
                                                                <label class="label_espacio documentaciones_7">FUD</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div class=" col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_8" type="checkbox" value="Informe de accidente" v-model="documentacion" @change="documenteshion(8,'Informe de accidente','No','')" name="documentacion[]_8" v-bind:checked="existeDocumento('Informe de accidente') == true " /> 
                                                                <label class="label_espacio documentaciones_8">Informe de accidente</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_9" type="checkbox" value="Licencia" v-model="documentacion" @change="documenteshion(9,'Licencia','No','')" name="documentacion[]_9" v-bind:checked="existeDocumento('Licencia') == true " /> 
                                                                <label class="label_espacio documentaciones_9">Licencia</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_10" type="checkbox" value="Parte policivo" v-model="documentacion" @change="documenteshion(10,'Parte policivo','No','')" name="documentacion[]_10" v-bind:checked="existeDocumento('Parte policivo') == true " /> 
                                                                <label class="label_espacio documentaciones_10">Parte policivo</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_11" type="checkbox" value="Presupuestos" v-model="documentacion" @change="documenteshion(11,'Presupuestos','No','')" name="documentacion[]_11" v-bind:checked="existeDocumento('Presupuestos') == true " /> 
                                                                <label class="label_espacio documentaciones_11">Presupuestos</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div class=" col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_12" type="checkbox" value="Resolución de tránsito" v-model="documentacion" @change="documenteshion(12,'Resolución de tránsito','No','')" name="documentacion[]_12" v-bind:checked="existeDocumento('Resolución de tránsito') == true " /> 
                                                                <label class="label_espacio documentaciones_12">Resolución de tránsito</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_13" type="checkbox" value="Traspaso" v-model="documentacion" @change="documenteshion(13,'Traspaso','No','')" name="documentacion[]_13" v-bind:checked="existeDocumento('Traspaso') == true " /> 
                                                                <label class="label_espacio documentaciones_13">Traspaso</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-2" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input type="checkbox" value="Cédula" v-model="documentacion" @change="documenteshion(2,'Cédula','No','')" name="documentacion[]_2" v-bind:checked="existeDocumento('Cédula') == true " /> 
                                                                <label class="label_espacio documentaciones_2">Cédula</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_14" type="checkbox" value="Formato de tránsito" v-model="documentacion" @change="documenteshion(14,'Formato de tránsito','No','')" name="documentacion[]_14" v-bind:checked="existeDocumento('Formato de tránsito') == true " /> 
                                                                <label class="label_espacio documentaciones_14">Formato de tránsito</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_9" type="checkbox" value="Licencia" v-model="documentacion" @change="documenteshion(9,'Licencia','No','')" name="documentacion[]_9" v-bind:checked="existeDocumento('Licencia') == true " /> 
                                                                <label class="label_espacio documentaciones_9">Licencia</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_15" type="checkbox" value="Poder firmado" v-model="documentacion" @change="documenteshion(15,'Poder firmado','No','')" name="documentacion[]_15" v-bind:checked="existeDocumento('Poder firmado') == true " /> 
                                                                <label class="label_espacio documentaciones_15">Poder firmado</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-3" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_16" type="checkbox" value="Aviso de operación" v-model="documentacion" @change="documenteshion(16,'Aviso de operación','No','')" name="documentacion[]_16" v-bind:checked="existeDocumento('Aviso de operación') == true " /> 
                                                                <label class="label_espacio documentaciones_16">Aviso de operación</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input type="checkbox" value="Cédula" v-model="documentacion" @change="documenteshion(2,'Cédula','No','')" name="documentacion[]_2" v-bind:checked="existeDocumento('Cédula') == true " /> 
                                                                <label class="label_espacio documentaciones_2">Cédula</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_6" type="checkbox" value="Fotos" v-model="documentacion" @change="documenteshion(6,'Fotos','No','')" name="documentacion[]_6" v-bind:checked="existeDocumento('Fotos') == true " /> 
                                                                <label class="label_espacio documentaciones_6">Fotos</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_7" type="checkbox" value="FUD" v-model="documentacion" @change="documenteshion(7,'FUD','No','')" name="documentacion[]_7" v-bind:checked="existeDocumento('FUD') == true " /> 
                                                                <label class="label_espacio documentaciones_7">FUD</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_8" type="checkbox" value="Informe  de accidente" v-model="documentacion" @change="documenteshion(8,'Informe  de accidente','No','')" name="documentacion[]_8" v-bind:checked="existeDocumento('Informe  de accidente') == true " /> 
                                                                <label class="label_espacio documentaciones_8">Informe  de accidente</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_9" type="checkbox" value="Licencia" v-model="documentacion" @change="documenteshion(9,'Licencia','No','')" name="documentacion[]_9" v-bind:checked="existeDocumento('Licencia') == true " /> 
                                                                <label class="label_espacio documentaciones_9">Licencia</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_17" type="checkbox" value="Registro único vehícular" v-model="documentacion" @change="documenteshion(17,'Registro único vehícular','No','')" name="documentacion[]_17" v-bind:checked="existeDocumento('Registro único vehícular') == true " /> 
                                                                <label class="label_espacio documentaciones_17">Registro único vehícular</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_12" type="checkbox" value="Resolución de tránsito" v-model="documentacion" @change="documenteshion(12,'Resolución de tránsito','No','')" name="documentacion[]_12" v-bind:checked="existeDocumento('Resolución de tránsito') == true " /> 
                                                                <label class="label_espacio documentaciones_12">Resolución de tránsito</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-4" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_18" type="checkbox" value="Denuncia original" v-model="documentacion" @change="documenteshion(18,'Denuncia original','No','')" name="documentacion[]_18" v-bind:checked="existeDocumento('Denuncia original') == true " /> 
                                                                <label class="label_espacio documentaciones_18">Denuncia original</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input type="checkbox" value="Cédula" v-model="documentacion" @change="documenteshion(2,'Cédula','No','')" name="documentacion[]_2" v-bind:checked="existeDocumento('Cédula') == true " /> 
                                                                <label class="label_espacio documentaciones_2">Cédula</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_3" type="checkbox" value="Cotización" v-model="documentacion" @change="documenteshion(3,'Cotización','No','')" name="documentacion[]_3" v-bind:checked="existeDocumento('Cotización') == true " /> 
                                                                <label class="label_espacio documentaciones_3">Cotización</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_6" type="checkbox" value="Fotos" v-model="documentacion" @change="documenteshion(6,'Fotos','No','')" name="documentacion[]_6" v-bind:checked="existeDocumento('Fotos') == true " /> 
                                                                <label class="label_espacio documentaciones_6">Fotos</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_11" type="checkbox" value="Presupuestos" v-model="documentacion" @change="documenteshion(11,'Presupuestos','No','')" name="documentacion[]_11" v-bind:checked="existeDocumento('Presupuestos') == true " /> 
                                                                <label class="label_espacio documentaciones_11">Presupuestos</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_13" type="checkbox" value="Traspaso" v-model="documentacion" @change="documenteshion(13,'Traspaso','No','')" name="documentacion[]_13" v-bind:checked="existeDocumento('Traspaso') == true " /> 
                                                                <label class="label_espacio documentaciones_13">Traspaso</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-5" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                        <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_19" type="checkbox" value="Entrada Cuarto Ugencias" v-model="documentacion" @change="documenteshion(19,'Entrada Cuarto Ugencias','No','')" name="documentacion[]_19" v-bind:checked="existeDocumento('Entrada Cuarto Ugencias') == true " /> 
                                                                <label class="label_espacio documentaciones_19">Entrada Cuarto Ugencias</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_20" type="checkbox" value="Facturas con Recetas" v-model="documentacion" @change="documenteshion(20,'Facturas con Recetas','No','')" name="documentacion[]_20" v-bind:checked="existeDocumento('Facturas con Recetas') == true " /> 
                                                                <label class="label_espacio documentaciones_20">Facturas con Recetas</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_21" type="checkbox" value="Recibos de atención médica" v-model="documentacion" @change="documenteshion(21,'Recibos de atención médica','No','')" name="documentacion[]_21" v-bind:checked="existeDocumento('Recibos de atención médica') == true " /> 
                                                                <label class="label_espacio documentaciones_21">Recibos de atención médica</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>                                                
                                            </div>
                                        </div>
                                        <div id="tab-6" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_0" type="checkbox" value="Aviso Operaciones" v-model="documentacion" @change="documenteshion(0,'Aviso Operaciones','No','')" name="documentacion[]_0" v-bind:checked="existeDocumento('Aviso Operaciones') == true " /> 
                                                                <label class="label_espacio documentaciones_0">Aviso Operaciones</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_22" type="checkbox" value="Facturas" v-model="documentacion" @change="documenteshion(22,'Facturas','No','')" name="documentacion[]_22" v-bind:checked="existeDocumento('Facturas') == true " /> 
                                                                <label class="label_espacio documentaciones_22">Facturas</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_6" type="checkbox" value="Fotos" v-model="documentacion" @change="documenteshion(6,'Fotos','No','')" name="documentacion[]_6" v-bind:checked="existeDocumento('Fotos') == true " /> 
                                                                <label class="label_espacio documentaciones_6">Fotos</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_7" type="checkbox" value="FUD" v-model="documentacion" @change="documenteshion(7,'FUD','No','')" name="documentacion[]_7" v-bind:checked="existeDocumento('FUD') == true " /> 
                                                                <label class="label_espacio documentaciones_7">FUD</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div class=" col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_11" type="checkbox" value="Presupuestos" v-model="documentacion" @change="documenteshion(11,'Presupuestos','No','')" name="documentacion[]_11" v-bind:checked="existeDocumento('Presupuestos') == true " /> 
                                                                <label class="label_espacio documentaciones_11">Presupuestos</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_17" type="checkbox" value="Registro único vehicular" v-model="documentacion" @change="documenteshion(17,'Registro único vehicular','No','')" name="documentacion[]_17" v-bind:checked="existeDocumento('Registro único vehicular') == true " /> 
                                                                <label class="label_espacio documentaciones_17">Registro único vehicular</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                            <div id="campo_documentacion" class="row" style="vertical-align: middle;">
                                                                <input id="documentacion_12" type="checkbox" value="Resolución de tránsito" v-model="documentacion" @change="documenteshion(12,'Resolución de tránsito','No','')" name="documentacion[]_12" v-bind:checked="existeDocumento('Resolución de tránsito') == true " /> 
                                                                <label class="label_espacio documentaciones_12">Resolución de tránsito</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin TabPanel -->
            <br>
            <input type="hidden" value="reclamo" id="modulo" name="campodocumentacion[modulo]" class="modulo">
            <input type="hidden" value="{{documentacion}}" id="opcion" name="campodocumentacion[opcion]" class="opcion">
            <input type="hidden" value="23" id="cantidad_check" name="campodocumentacion[cantidad_check]" class="cantidad_check">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <label class="nombre_doc_titulo" id="nombre_doc_titulo">Nombre del documento</label>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php for ($j = 0; $j <= 23; $j++) { ?>

                <div id='file_tools_solicitudes_<?php echo $j ?>'>
                </div>

                <?php } ?>
            </div>
            <div class="docentregados_crear row" id="docentregados_crear">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <label class="nombre_doc_titulo" id="nombre_doc_titulo">Nombre del documento</label>
                </div>
                <div style="margin-bottom: 25px;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div class='file_upload_solicitudes_adicionales' id='h1'>
                        <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" />
                        <input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                        <input type="hidden" value="new" name="campotipodoc[]">
                        <br><br>
                    </div>
                    <div id='file_tools_solicitudes_adicionales'>
                        <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_solicitudes_adicionales"><i class="fa fa-plus"></i>
                        </button>
                        <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_solicitudes_adicionales"><i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>        
    </div>
</div>
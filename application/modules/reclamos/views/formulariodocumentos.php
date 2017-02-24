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
                        <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
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
                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
                                        <li class="" id="tab_planes"><a data-toggle="tab" href="#tab-6" aria-expanded="false"  data-targe="beneficios">Planes</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane active">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-2" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-3" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-4" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tab-5" class="tab-pane">
                                            <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                                                <div class="tab-content row">
                                                    <div class="ibox-content tab-pane fade in active">
                                                        <div>
                                                            <div id="campo_documentacion" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 row" style="vertical-align: middle;">
                                                                <input id="documentacion_0" type="checkbox" value="Entrada Cuarto Ugencias" v-model="documentacion" @click="documenteshion(0,'Entrada Cuarto Ugencias','No','')" name="documentacion[]_0" /> 
                                                                <label class="label_espacio documentaciones_0">Entrada Cuarto Ugencias</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div id="campo_documentacion" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 row" style="vertical-align: middle;">
                                                                <input id="documentacion_1" type="checkbox" value="Facturas con Recetas" v-model="documentacion" @click="documenteshion(1,'Facturas con Recetas','No','')" name="documentacion[]_1" /> 
                                                                <label class="label_espacio documentaciones_1">Facturas con Recetas</label>
                                                                <span id="error_check"></span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div id="campo_documentacion" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 row" style="vertical-align: middle;">
                                                                <input id="documentacion_2" type="checkbox" value="Recibos de atención médica" v-model="documentacion" @click="documenteshion(2,'Recibos de atención médica','No','')" name="documentacion[]_2" /> 
                                                                <label class="label_espacio documentaciones_2">Recibos de atención médica</label>
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
            <input type="hidden" value="3" id="cantidad_check" name="campodocumentacion[cantidad_check]" class="cantidad_check">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <label class="nombre_doc_titulo" id="nombre_doc_titulo">Nombre del documento</label>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php for ($j = 0; $j <= 3; $j++) { ?>

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
                        <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
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
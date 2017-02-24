<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php if (count($campos['documentacion']) > 0) { ?>


                    <h5 style="font-size:14px">Documentos para tr&aacute;mites</h5>
                    <hr style="margin-top:10px!important;">
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
                            <input <?php if ($doc->categoria == "Obligatorio") { ?> data-rule-required="true" <?php } ?> id="documentacion_<?php print $i; ?>" type="checkbox" value="<?php print $doc->nombre; ?>" v-model="documentacion" @click="documenteshion(<?php print $i; ?>,'<?php echo $doc->nombre; ?>','<?php echo $requerido; ?>','<?php echo $doc->modulo; ?>')" name="documentacion[]_<?php print $i; ?>" /> <label class="label_espacio documentaciones_<?php print $i; ?>">

                                <?php
                                print $doc->nombre;
                                if ($doc->categoria == "Obligatorio") {
                                    ?><span required="" aria-required="true">*</span><?php } ?></label>
                            <span id="error_check"></span>
                        </div>
                    <?php } ?>
                    <input type="hidden" value="{{modulo}}" id="modulo" name="campodocumentacion[modulo]" class="modulo">
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
            <div style="margin-bottom: 25px;" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">

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
                <h5 style="font-size:14px">Documentos para tr&aacute;mites</h5>
                <hr style="margin-top:10px!important;">
                <div class="row">
                    <div v-for="doc in documentacionesList" class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="vertical-align: middle;">
                        <label v-if='doc.categoria == "Obligatorio"'>
                        <input id="documentacion_{{$index}}" style="margin-left: 5px!important;" type="checkbox" v-bind:value="doc.nombre" v-model="documentacion" @click="documenteshion($index,doc.nombre,doc.modulo,'1')" v-bind:data-valor="documentacionesgbd[$index].valor" v-bind:data-nombre="doc.nombre" name="documentacion_{{$index}}"  v-bind:checked="doc.nombre==arrayauxiliar[doc.nombre]" data-rule-required="true"/>
                        </label>
                        <label v-if='doc.categoria != "Obligatorio"'>
                        <input id="documentacion_{{$index}}" style="margin-left: 5px!important;" type="checkbox" v-bind:value="doc.nombre" v-model="documentacion" @click="documenteshion($index,doc.nombre,doc.modulo,'1')" v-bind:data-valor="documentacionesgbd[$index].valor" v-bind:data-nombre="doc.nombre" name="documentacion_{{$index}}"  v-bind:checked="doc.nombre==arrayauxiliar[doc.nombre]"/>
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
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-6">
                    <label>Estado</label>
                    <div class="input-group">            
                        <select  name="campo[estado]" class="form-control" id="estado" :disabled="disabledEstados">
                            <?php
                            $en_tramite = "";
                            $aprobada = "";
                            $rechazada = "";
                            $pendiente = "";
                            $anulada = "";
                            if (isset($campos['estado']) && $campos['estado'] == "En trámite") {
                                $en_tramite = "selected";
                            } else if (isset($campos['estado']) && $campos['estado'] == "Aprobada") {
                                $aprobada = "selected";
                            } else if (isset($campos['estado']) && $campos['estado'] == "Rechazada") {
                                $rechazada = "selected";
                            } else if (isset($campos['estado']) && $campos['estado'] == "Pendiente") {
                                $pendiente = "selected";
                            } else if (isset($campos['estado']) && $campos['estado'] == "Anulada") {
                                $anulada = "selected";
                            }
                            if ($campos['politicas_general'] > 0 && isset($campos['estado'])) {
                                if (in_array(21, $campos['politicas']) || in_array(22, $campos['politicas']) || in_array(23, $campos['politicas'])) {
                                    if ($campos['estado'] == "En trámite") {
                                        if (in_array(21, $campos['politicas'])) {
                                            ?>
                                            <option value='En trámite' <?php echo $en_tramite ?> >En trámite</option>
                                            <option value='Aprobada' <?php echo $aprobada ?> >Aprobada</option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value='En trámite' <?php echo $en_tramite ?> >En trámite</option>
                                            <?php
                                        }
                                    } else if ($campos['estado'] == "Anulada") {
                                        if (in_array(22, $campos['politicas'])) {
                                            ?>
                                            <option value='Anulada' <?php echo $anulada ?> >Anulada</option>
                                            <option value='Pendiente' <?php echo $pendiente ?> >Pendiente</option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value='Anulada' <?php echo $anulada ?> >Anulada</option>
                                            <?php
                                        }
                                    } else if ($campos['estado'] == "Rechazada") {
                                        if (in_array(23, $campos['politicas'])) {
                                            ?>
                                            <option value='Rechazada' <?php echo $rechazada ?> >Rechazada</option>
                                            <option value='Pendiente' <?php echo $pendiente ?> >Pendiente</option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value='Rechazada' <?php echo $rechazada ?> >Rechazada</option>
                                            <?php
                                        }
                                    }
                                } else {
                                    ?>
                                    <option v-for="estList in catalogoEstado" v-bind:value="estList.etiqueta" :selected="estList.etiqueta==estado">{{{estList.etiqueta}}}</option>
                                    <?php
                                }
                            } else {
                                ?>
                                <option v-for="estList in catalogoEstado" v-bind:value="estList.etiqueta" :selected="estList.etiqueta==estado">{{{estList.etiqueta}}}</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div> 
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2" ></div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2" style="margin-left: -25px;">
                    <label>Asignado a</label>
                    <div class="input-group">            
                        <select  name="campo[usuario_id]" class="form-control" id="usuario_id">
                            <option v-for="usersList in listadoUsuarios" v-bind:value="usersList.id" :selected="usersList.id==usuario_id">
                                {{{usersList.nombre+" "+usersList.apellido}}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2" style="margin-left: -20px;"> <!-- float:right;   -->
                    <label>Centro Contable</label>
                    <select name="campo[centro_contable]" id="centro_contable" class="form-control">
                        <option value="0">Seleccione</option>
                        <option v-for="centro in centros_contables" v-bind:value="centro.id" :selected="centro.id == id_centro">{{centro.nombre}}</option>
                    </select>
                </div>

            </div>
        </div>
    </div>
</div>

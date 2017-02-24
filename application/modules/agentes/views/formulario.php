<?php
$info = !empty($info) ? $info : array();
if (!empty($info['agente'])) {
    if ($info['agente']['letra'] == 'E' || $info['agente']['letra'] == 'N' || $info['agente']['letra'] == 'PE' || $info['agente']['letra'] == 'PI' || $info['agente']['letra'] == 0) {
        $tipo_letra = "CE";
    } else {
        $tipo_letra = $info['agente']['letra'];
    }
}

$disabled = "";
if ($info['guardar'] == 0)
    $disabled = 'disabled="disabled"';
?>
 <input type="hidden" id="contador" name="contador" class="contador">
<div id="vistaAgente">
    <div class="tab-content">
        <div id="datosdelagente-5" class="tab-pane active">
            <?php echo form_open(base_url('agentes/guardar'), $info['form']); ?>
            <?php if (isset($info['agente'])) { ?>
                <input type="hidden" name="campo[uuid]" id="campo[uuid]"
                       value="<?php echo $info['agente']['uuid_agente'] ?>">
                   <?php } ?>
            <div class="ibox">
                <div class="ibox-content m-b-sm" style="display: block; border:0px">
                    <div class="row">
                        <input type="hidden" name="campo[empresa_id]" value="<?php echo $info['id_empresa']; ?>">
                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 nombreAgente"><label>Nombre
                                <span required="" aria-required="true">*</span></label>
                            <input type="text" name="campo[nombre]" id="campo[nombre]" value="<?php
                            if (isset($info['agente'])) {
                                echo $info['agente']['nombre'];
                            }
                            ?>" class="form-control" id="campo[nombre]" data-rule-required="true">
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                            <label>Identificaci&oacute;n <span required="" aria-required="true">*</span></label>
                            <select data-rule-required="true" class="form-control tipo_identificacion" name="campo[tipo_identificacion]">
                                <option value="">Seleccione</option>
                                <?php foreach ($info['tipo_identificacion'] as $tipo_identificacion) { ?>
                                    <option <?php
                                    if (isset($info['agente'])) {
                                        if ($tipo_identificacion->valor == $info['agente']['tipo_identificacion']) {
                                            echo ' selected';
                                        }
                                    }
                                    ?> value="<?php echo $tipo_identificacion->valor ?>"><?php echo $tipo_identificacion->etiqueta ?></option>
                                    <?php } ?>
                            </select>
                        </div>
                        <div class="noPAS row">
                            <div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
                                <label>Provincia <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control provincia" name="campo[provincia]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['provincias'] as $provincia) { ?>
                                        <option <?php
                                        if (isset($info['agente']['provincia'])) {
                                            if ($provincia->key == $info['agente']['provincia']) {
                                                echo ' selected';
                                            }
                                        }
                                        ?> value="<?php echo $provincia->key ?>"><?php echo $provincia->etiqueta ?></option>
                                        <?php } ?>
                                </select>
                            </div>                            
                            <div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
                                <label>Letras <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control letra" name="campo[letra]" id="campo[letra]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['letras'] as $letra) { ?>
                                        <?php if ($letra->key != "PAS" && $letra->key != "RUC") { ?>
                                            <option <?php if (isset($info['agente']['letra'])) { ?> 
                                                <?php
                                                if ($letra->key == $info['agente']['letra']) {
                                                    echo ' selected';
                                                }
                                                ?>
                                                <?php
                                            } elseif ($letra->key == '0') {
                                                echo ' selected';
                                            }
                                            ?> value="<?php
                                                if ($letra->key == '0') {
                                                    echo 'cero';
                                                } else {
                                                    echo $letra->key;
                                                }
                                                ?>"><?php echo $letra->etiqueta ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                </select>
                            </div>                           
                            <div class="form-group col-xs-6 col-sm-3 col-md-1 col-lg-1">
                                <label>Tomo <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" value="<?php
                                if (isset($info['agente']['tomo'])) {
                                    echo $info['agente']['tomo'];
                                }
                                ?>" type="text" id="campo[tomo]" name="campo[tomo]" class="form-control">
                            </div>
                            <div class="form-group col-xs-6 col-sm-3 col-md-1 col-lg-1">
                                <label>Asiento <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" value="<?php
                                if (isset($info['agente']['asiento'])) {
                                    echo $info['agente']['asiento'];
                                }
                                ?>" type="text" id="campo[asiento]" name="campo[asiento]" class="form-control">
                            </div>
                        </div>
                        <div class="RUC">
                            <div class="form-group col-xs-12 col-sm-3 col-md-1 col-lg-1">
                                <label>Tomo <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" value="<?php
                                if (isset($info['agente']['tomo_ruc'])) {
                                    echo $info['agente']['tomo_ruc'];
                                }
                                ?>" type="text" id="campo[tomo_ruc]" name="campo[tomo_ruc]" class="form-control">
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                <label>Folio/Imagen/Documento <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" value="<?php
                                if (isset($info['agente']['folio'])) {
                                    echo $info['agente']['folio'];
                                }
                                ?>" type="text" id="campo[folio]" name="campo[folio]" class="form-control">
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                <label>Asiento/Ficha <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" value="<?php
                                if (isset($info['agente']['asiento_ruc'])) {
                                    echo $info['agente']['asiento_ruc'];
                                }
                                ?>" type="text" id="campo[asiento_ruc]" name="campo[asiento_ruc]" class="form-control">
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                <label>Digito Verificador <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" value="<?php
                                if (isset($info['agente']['digito'])) {
                                    echo $info['agente']['digito'];
                                }
                                ?>" type="text" id="campo[digito]" name="campo[digito]" class="form-control">
                            </div>    
                        </div>

                        <div class="PAS">
                            <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                <label>No. Pasaporte <span required="" aria-required="true">*</span></label>
                                <input data-rule-required="true" value="<?php
                                if (isset($info['agente']['pasaporte'])) {
                                    echo $info['agente']['pasaporte'];
                                }
                                ?>" type="text"  id="campo[pasaporte]" name="campo[pasaporte]" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 ">
                            <label>Teléfono </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                <input type="input-left-addon" name="campo[telefono]"
                                       value="<?php
                                       if (isset($info['agente'])) {
                                           echo $info['agente']['telefono'];
                                       }
                                       ?>" class="form-control" data-inputmask="'mask': '999-9999', 'greedy':true"
                                       id="campo[telefono]"> </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 ">
                            <label>Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-addon">@</span><input type="input-left-addon" name="campo[correo]"
                                                                               value="<?php
                                                                               if (isset($info['agente'])) {
                                                                                   echo $info['agente']['correo'];
                                                                               }
                                                                               ?>" data-rule-email="true"
                                                                               class="form-control debito" id="campo[correo]"></div>
                            <label for="campo[correo]" generated="true" class="error"></label>
                        </div>


                        <div class="form-group col-xs-12 col-sm-8 col-md-6 col-lg-6 ">
                            <table  id="tabla_ramos_parti" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 44%; "><label>Ramo</label></th>
                                        <th style="width: 2%; "></th>
                                        <th style="width: 44%; "><label>Participación</label></th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if (isset($info['agente']['countramos']) && $info['agente']['countramos'] > 0) {
                                        $contador = 1;
                                        foreach ($info['agente']['ramos'] as $val) {
                                            ?>
                                            <tr>
                                                <td style="margin-right: 5px">
                                                    <select  data-placeholder="Seleccione una opción" name="ramos[]" id="campo[ramo]" class="form-control chosen-select" multiple>
                                                        <option value="">Seleccione</option>
                                                        <?php
                                                        $cont = 0;
                                                        foreach ($info['menu_crear'] AS $menu) {
                                                            foreach ($info['menu_crear'] AS $value) {
                                                                if ($menu['id'] == $value['padre_id']) {
                                                                    $cont++;
                                                                }
                                                            }

                                                            if ($cont == 0 && $menu['padre_id'] != 0) {
                                                                if ($menu['estado'] == 1) {
                                                                    ?>

                                                                    <option value="<?php echo $menu['id'] ?>" <?php
                                                                    if ($val['id_ramo'] == $menu['id']) {
                                                                        echo " selected";
                                                                    }
                                                                    ?> ><?php echo $menu['nombre'] ?></option>
                                                                        <?php } else { ?>
                                                                    <option style="color: red;" value="<?php echo $menu['id'] ?>" <?php
                                                                    if ($val['id_ramo'] == $menu['id']) {
                                                                        echo " selected";
                                                                    }
                                                                    ?> ><?php echo $menu['nombre'] ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    $cont = 0;
                                                                }
                                                                ?>
                                                    </select>
                                                    <label for="campo[ramo]" generated="true" class="error"></label>
                                                </td>
                                                <td></td>
                                                <td style="margin-right: 5px">
                                                    <div class="input-group">
                                                        <input type="input-left-addon" name="porcentaje_participacion[]" value="<?php echo $val['participacion']; ?>" class="form-control" data-inputmask="'mask': '9{1,15}.99', 'greedy':true" id="porcentaje_participacion">
                                                        <span class="input-group-addon">%</span>
                                                    </div>
                                                    <label for="campo[porcentaje_participacion]" generated="true" class="error"></label>
                                                </td>
                                                <td>
                                                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;" >
                                                        <?php if ($info['agente']['countramos'] == $contador) { ?>
                                                            <a onclick="agregarfila(this, 'tabla_ramos_parti');" class="btn btn-default" id="agregarbtn" style="margin-top: -20px; display: none"><i class="fa fa-plus fa-1"></i></a>
                                                            <a onclick="eliminarfila(this);" class="btn btn-default" id="eliminarbtn" style="margin-top: -20px;"><i class="fa fa-trash fa-1"></i></a>
                                                        <?php } else { ?>
                                                            <a onclick="agregarfila(this, 'tabla_ramos_parti');" class="btn btn-default" id="agregarbtn" style="margin-top: -20px;display: none"><i class="fa fa-plus fa-1"></i></a>
                                                            <a onclick="eliminarfila(this);" class="btn btn-default" id="eliminarbtn" style="margin-top: -20px;"><i class="fa fa-trash fa-1"></i></a>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                               
                                            </tr>
                                            <?php
                                            $contador++;
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td style="margin-right: 5px">
                                                <select  data-placeholder="Seleccione una opción" name="ramos[]" id="campo[ramo]" class="form-control chosen-select" multiple >
                                                    <option value="">Seleccione</option>
                                                    <?php
                                                    $cont = 0;
                                                    foreach ($info['menu_crear'] AS $menu) {
                                                        foreach ($info['menu_crear'] AS $value) {
                                                            if ($menu['id'] == $value['padre_id']) {
                                                                $cont++;
                                                            }
                                                        }
                                                        if ($cont == 0 && $menu['padre_id'] != 0) {
                                                            if ($menu['estado'] == 1) {
                                                                echo '<option value="' . $menu['id'] . '">' . $menu['nombre'] . '</option>';
                                                            } else {
                                                                echo '<option style="color: red;" value="' . $menu['id'] . '">' . $menu['nombre'] . '</option>';
                                                            }
                                                        }
                                                        $cont = 0;
                                                    }
                                                    ?>
                                                </select>
                                                <label for="campo[ramo]" generated="true" class="error"></label>
                                            </td>
                                            <td></td>
                                            <td style="margin-right: 5px">
                                                <div class="input-group">
                                                    <input type="input-left-addon" name="porcentaje_participacion[]" value="" class="form-control" data-inputmask="'mask': '9{1,15}.99', 'greedy':true" id="porcentaje_participacion">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                                <label for="campo[porcentaje_participacion]" generated="true" class="error"></label>
                                            </td>
                                            <td>
                                                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;" >
                                                    <a onclick="eliminarfila(this);" class="btn btn-default" id="eliminarbtn" style="margin-top: -20px;"><i class="fa fa-trash fa-1"></i></a>

                                                </div>
                                            </td>

                                        </tr>
                                        <?php
                                    }
                                    ?>                                    
                                </tbody>
                            </table>


                        </div>     

                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;" >
                        <a onclick="agregarfila(this, 'tabla_ramos_parti');" class="btn btn-default" id="agregarbtn" style="margin-top: -170px; margin-left: 100%;"><i class="fa fa-plus fa-1"></i></a>

                    </div>


                    <?php if (!isset($info['estadoAgente']) OR $info['estadoAgente'] != 0) { ?>
                        
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
                                <label>Estado <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control estado" name="campo[estado]">
                                    <?php
                                    if ($info['politicas_general'] > 0) {
                                        if ((in_array(16, $info['politicas']) || in_array(17, $info['politicas']) || in_array(18, $info['politicas']))) {
                                            if ($info['agente']['estado'] == "Por Aprobar") {
                                                if (in_array(16, $info['politicas'])) {
                                                    foreach ($info['estado'] as $estado) {
                                                        if ($estado->etiqueta != "Inactivo") {
                                                            ?>
                                                            <option <?php
                                                            if ($estado->valor == "por_aprobar") {
                                                                echo ' selected';
                                                            }
                                                            ?> value="<?php echo $estado->etiqueta ?>"><?php echo $estado->etiqueta ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                    <option value='<?php echo $info['agente']['estado']; ?>'><?php echo $info['agente']['estado']; ?></option>
                                                    <?php
                                                }
                                            } else if ($info['agente']['estado'] == "Activo") {
                                                if (in_array(17, $info['politicas'])) {
                                                    foreach ($info['estado'] as $estado) {
                                                        if ($estado->etiqueta != "Por Aprobar") {
                                                            ?>
                                                            <option <?php
                                                            if ($estado->valor == "por_aprobar") {
                                                                echo ' selected';
                                                            }
                                                            ?> value="<?php echo $estado->etiqueta ?>"><?php echo $estado->etiqueta ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                    <option value='<?php echo $info['agente']['estado']; ?>'><?php echo $info['agente']['estado']; ?></option>
                                                    <?php
                                                }
                                            } else if ($info['agente']['estado'] == "Inactivo") {
                                                if (in_array(18, $info['politicas'])) {
                                                    foreach ($info['estado'] as $estado) {
                                                        if ($estado->etiqueta != "Por Aprobar") {
                                                            ?>
                                                            <option <?php
                                                            if ($estado->valor == "por_aprobar") {
                                                                echo ' selected';
                                                            }
                                                            ?> value="<?php echo $estado->etiqueta ?>"><?php echo $estado->etiqueta ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                    <option value='<?php echo $info['agente']['estado']; ?>'><?php echo $info['agente']['estado']; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                foreach ($info['estado'] as $estado) {
                                                    ?>
                                                    <option <?php
                                                    if ($estado->valor == "por_aprobar") {
                                                        echo ' selected';
                                                    }
                                                    ?> value="<?php echo $estado->etiqueta ?>"><?php echo $estado->etiqueta ?></option>
                                                        <?php
                                                    }
                                                }
                                            } else {
                                                ?>
                                            <option value='<?php echo $info['agente']['estado']; ?>'><?php echo $info['agente']['estado']; ?></option>
                                            <?php
                                        }
                                    } else {
                                        foreach ($info['estado'] as $estado) {
                                            if (isset($info['agente']['estado'])) {
                                                $agtestado = $info['agente']['estado'];
                                            }else{
                                                $agtestado = "Por Aprobar";
                                            }
                                            ?>
                                            <option <?php if ($estado->valor == $agtestado) { echo ' selected'; } ?> value="<?php echo $estado->etiqueta ?>"><?php echo $estado->etiqueta ?></option>
                                                <?php
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>

                    <?php } ?>

                    <div class="row botones">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" ><a
                                href="<?php echo base_url('agentes/listar'); ?>" class="btn btn-default btn-block"
                                id="cancelar">Cancelar </a>
                        </div>

                        <?php if ($info['guardar'] == 1) { ?>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                <input type="submit" name="campo[guardar]" value="Guardar "
                                       class="btn btn-primary btn-block" id="campo[guardar]">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
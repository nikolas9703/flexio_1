<div id="vistaCliente" class="">

    <div class="tab-content">
        <?php
        $formAttr = array(
            'method' => 'POST',
            'id' => 'formAjustadoresCrear',
            'autocomplete' => 'off'
        );
        if (isset($campos['uuid_ajustadores']) && ($campos['uuid_ajustadores'] != "")) {
            echo form_open(base_url('ajustadores/editar'), $formAttr);
        } else {
            echo form_open(base_url('ajustadores/guardar'), $formAttr);
        }
        $disabled = "";
        if ($campos['guardar'] == 0)
            $disabled = 'disabled="disabled"';
        ?>

        <?php echo form_open(base_url('ajustadores/guardar'), $formAttr); ?>
        <div id="datosdelcliente-5" class="tab-pane active col-lg-12 col-md-12">
            <input type="hidden" name="campo[uuid]" id="campo[uuid]" <?php if (isset($campos['uuid_ajustadores'])) { ?>value="<?php
                echo $campos['uuid_ajustadores'];
            }
            ?>">
             <input type="hidden" name="campo[ruc]" id="campo[ruc]" <?php if (isset($campos['ruc'])) { ?>value="<?php
                echo $campos['ruc'];
            }
            ?>">

            <div class="ibox"> 
                <div class="ibox-title">
                    <h5>Datos del ajustador</h5>
                    <div class="ibox-content" style="display: block;" id="datosGenerales">
                        <div class="row">
                            <div class="form-group col-xs-2">
                                <label>Nombre ajustador <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[nombre]" <?php echo $disabled ?> class="form-control nombre" id="campo[nombre]" data-rule-required="true" <?php if (isset($campos['nombre'])) { ?>value="<?php
                                    echo $campos['nombre'];
                                }
                                ?>">
                            </div>
                            <div class="form-group col-xs-2">
                                <label>Identificación <span required="" aria-required="true">*</span></label>                               
                                <select <?php echo $disabled ?> data-rule-required="true" id="campo[identificacion]" class="form-control identificacion" name="campo[identificacion]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['identificacion'] as $identificacion) { ?>
                                        <option <?php
                                        if (isset($campos['identificacion'])) {
                                            if ($identificacion->valor == $campos['identificacion']) {
                                                echo ' selected';
                                            }
                                        }
                                        ?> value="<?php echo $identificacion->valor ?>"><?php echo $identificacion->valor ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="juridico" id="juridico" name="juridico">                            
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <label>Tomo/Rollo<span required="" aria-required="true">*</span></label>
                                    <input <?php echo $disabled ?> type="text" name="campo[tomo_j]" class="form-control input-sm tomo_j" id="campo[tomo_j]" <?php if (isset($campos['tomo_j'])) { ?>value="<?php
                                        echo $campos['tomo_j'];
                                    }
                                    ?>">
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <label>Folio/Imágen/Documento<span required="" aria-required="true">*</span></label>
                                    <input <?php echo $disabled ?> type="text" name="campo[folio]" class="form-control input-sm folio" id="campo[folio]" <?php if (isset($campos['folio'])) { ?>value="<?php
                                        echo $campos['folio'];
                                    }
                                    ?>">
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <label> Asiento/Ficha<span required="" aria-required="true">*</span></label>
                                    <input <?php echo $disabled ?> type="text" name="campo[asiento_j]" class="form-control input-sm asiento_j" id="campo[asiento_j]" <?php if (isset($campos['asiento_j'])) { ?>value="<?php
                                        echo $campos['asiento_j'];
                                    }
                                    ?>">
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <label>D&iacute;gito verificador<span required="" aria-required="true">*</span></label>
                                    <input <?php echo $disabled ?> type="text" name="campo[digverificador]" class="form-control input-sm digverificador" id="campo[digverificador]" <?php if (isset($campos['digverificador'])) { ?>value="<?php
                                        echo $campos['digverificador'];
                                    }
                                    ?>">
                                </div>
                            </div>
                            <div class="natural" id="natural" name="natural">                            
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <label>Provincia<span required="" aria-required="true">*</span></label>
                                    <select <?php echo $disabled ?> class="form-control provincia" name="campo[provincia]" id="campo[provincia]">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($info['provincias'] as $provincia) { ?>
                                            <option <?php
                                            if (isset($campos['provincia'])) {
                                                if ($provincia->valor == $campos['provincia']) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $provincia->valor; ?>"><?php echo $provincia->valor; ?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <label>Letras<span required="" aria-required="true">*</span></label>
                                    <select <?php echo $disabled ?> id="campo[letras]" class="form-control letras" name="campo[letras]">
                                        <?php foreach ($info['letras'] as $letras) { ?>
                                            <option <?php
                                            if (isset($campo['letras'])) {
                                                if ($letras->valor == $campos['letras']) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $letras->valor ?>"><?php echo $letras->valor ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2 tomo">
                                    <label>Tomo<span required="" aria-required="true">*</span></label>
                                    <input <?php echo $disabled ?> type="text" name="campo[tomo]" class="form-control input-sm tomo_n" id="campo[tomo]" <?php if (isset($campos['tomo'])) { ?>value="<?php
                                        echo $campos['tomo'];
                                    }
                                    ?>">
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2 asiento">
                                    <label>Asiento<span required="" aria-required="true">*</span></label>
                                    <input <?php echo $disabled ?> type="text" name="campo[asiento]" class="form-control input-sm asiento_n" id="campo[asiento]" <?php if (isset($campos['asiento'])) { ?>value="<?php
                                        echo $campos['asiento'];
                                    }
                                    ?>">
                                </div>
                                <div class="form-group  col-xs-3 col-sm-3 col-md-3 col-lg-3 PAS">
                                    <label>No. Pasaporte<span required="" aria-required="true">*</span></label>
                                    <input <?php echo $disabled ?> type="text" name="campo[pasaporte]" class="form-control input-sm pasaporte" id="campo[pasaporte]" <?php if (isset($campos['pasaporte'])) { ?>value="<?php
                                        echo $campos['pasaporte'];
                                    }
                                    ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>Teléfono <span required="" aria-required="true">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input <?php echo $disabled ?> type="input-left-addon" name="campo[telefono]" class="form-control telefono" class="form-control" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]" data-rule-required="true" <?php if (isset($campos['telefono'])) { ?>value="<?php
                                        echo $campos['telefono'];
                                    }
                                    ?>">
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input <?php echo $disabled ?> type="input-left-addon" name="campo[email]" data-rule-email="true" class="form-control email"  id="campo[email]" <?php if (isset($campos['email'])) { ?>value="<?php
                                        echo $campos['email'];
                                    }
                                    ?>">
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Dirección" >
                                <label>Dirección</label>
                                <input <?php echo $disabled ?> type="text" name="campo[direccion]" class="form-control direccion" id="campo[direccion]" <?php if (isset($campos['direccion'])) { ?>value="<?php
                                    echo $campos['direccion'];
                                }
                                ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
                                <?php
                                $activo = "";
                                $por_abrobar = "";
                                $inactivo = "";

                                if ($campos['estado'] == "Por abrobar")
                                    $por_abrobar = "selected";
                                if ($campos['estado'] == "Activo")
                                    $activo = "selected";
                                if ($campos['estado'] == "Inactivo")
                                    $inactivo = "selected";
                                ?>
                                <label>Estado</label>
                                <select name="campo[estado]" id="campo[estado]" class="form-control" <?php echo $disabled ?> >
                                    <?php
                                    if ($campos['politicas_general'] > 0) {
                                        
                                        var_dump("holaaaaaaaaaaaaa");
                                        if ((in_array(13, $campos['politicas']) || in_array(14, $campos['politicas']) || in_array(15, $campos['politicas'])) && $campos['uuid_ajustadores'] != "") {
                                            if ($campos['estado'] == "Por aprobar") {
                                                if (in_array(13, $campos['politicas'])) {
                                                    ?>
                                                    <option value='Por aprobar' <?php echo $por_abrobar ?> >Por Aprobar</option>
                                                    <option value='Activo' <?php echo $activo ?> >Activo</option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value='Por aprobar' <?php echo $por_abrobar ?> >Por Aprobar</option>
                                                    <?php
                                                }
                                            } else if ($campos['estado'] == "Activo") {
                                                if (in_array(14, $campos['politicas'])) {
                                                    ?>
                                                    <option value='Activo' <?php echo $activo ?> >Activo</option>
                                                    <option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value='Activo' <?php echo $activo ?> >Activo</option>
                                                    <?php
                                                }
                                            } else if ($campos['estado'] == "Inactivo") {
                                                if (in_array(15, $campos['politicas'])) {
                                                    ?>
                                                    <option value='Activo' <?php echo $activo ?> >Activo</option>
                                                    <option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
                                                    <?php
                                                }
                                            }
                                        }else
                                        {
                                            ?>
                                            <option value='<?php echo $campos['estado']?>' ><?php echo $campos['estado']?></option>
                                            <?php
                                        }
                                    } else {
                                        if ($campos['estado'] == "Por aprobar" || $campos['estado'] == "") {
                                            ?>
                                            <option value='Por aprobar' <?php echo $por_abrobar ?> >Por Aprobar</option>
                                            <option value='Activo' <?php echo $activo ?> >Activo</option>
                                            <option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value='Activo' <?php echo $activo ?> >Activo</option>
                                            <option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>           
            <div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('ajustadores/listar'); ?>" class="btn btn-default btn-block cancelar" id="cancelar">Cancelar </a> </div>
<?php if ($campos['guardar'] == 1) { ?>
                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit">
                    </div>
            <?php } ?>
            </div>
<?php echo form_close(); ?>
        </div>
    </div>
</div>
<div id="formulariocontacto">
    <?php
    echo modules::run('ajustadores/ocultoformulariocontacto', $campos);
    ?>
</div>

</style>
<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5>Datos de la persona</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaPersona">
            <div class="tab-content">
                <div id="datosdelPersona-5" class="tab-pane active">
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar'), "id='persona'"); ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid" value="<?php if (isset($campos['id'])) print $campos['id'] ?>">
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_persona" class="tipo_id" value="5">
                    <input type="hidden" name="campo[idPersona]" value="<?php
                    if (isset($campos['id'])) {
                        print $campos['id'];
                    }
                    ?>" id="idPersona">
                    <input type="hidden" name="val" id="val" value="<?php echo !empty($_GET['val'])? $_GET['val'] : '' ; ?>" >
                    <input type="hidden" name="reg" id="reg" value="<?php echo !empty($_GET['reg'])? $_GET['reg'] : '' ; ?>" >
                    <input type="hidden" name="campo[validar_editar]" id="validar_editar" value="">
                    <input type="hidden" name="val" id="val" value="<?php echo !empty($_GET['val'])? $_GET['val'] : '' ; ?>" >
                    <input type="hidden" name="reg" id="reg" value="<?php echo !empty($_GET['reg'])? $_GET['reg'] : '' ; ?>" >
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 nombreAjustadores"><label>Nombre completo
                                    <span required="" aria-required="true">*</span></label>
                                    <input type="text" name="campo[nombrePersona]" id="nombrePersona" value="<?php if (isset($campos['datos'])) print($campos['datos']->nombrePersona) ?>" class="form-control">
                                </div>
                                <?php
                                if (isset($campos["datos"]["identificacion"])):
                                    $str = $campos["datos"]["identificacion"];
                                if (substr_count($str, '-')):
                                    $separateId = explode("-", $str);
                                if (count($separateId) == 3) {
                                    if(!is_numeric($separateId[0])){
                                        $letraVal = $separateId[0];
                                        $provinciaVal ="";
                                    }else{
                                        $provinciaVal =$separateId[0];           
                                        $letraVal = "0";
                                    }
                                    $tomo = $separateId[1];
                                    $asiento = $separateId[2];
                                } else {
                                    $provinciaVal = $separateId[0];
                                    $letraVal = $separateId[1];
                                    $tomo = $separateId[2];
                                    $asiento = $separateId[3];
                                }
                                $indentity = "Cédula";

                                else:
                                    $indentity = "pasaporte";
                                $pasaporte = $str;
                                endif;


                                endif;
                                ?>
                                <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                                    <label>Identificaci&oacute;n <span required="" aria-required="true">*</span></label>
                                    <select data-rule-required="true" class="form-control identificacion" name="campo[identificacion]" id="identificacion">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($tipo_identificacion as $tipo_iden) { ?>
                                        <option <?php
                                        if (isset($indentity))
                                            if (similar_text($tipo_iden->etiqueta, $indentity) >= 6)
                                                echo 'selected ';
                                            ?> 
                                            value="<?php echo $tipo_iden->valor ?>"><?php echo $tipo_iden->etiqueta ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div>
                                    <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 noPAS" >
                                        <label>Provincia <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control provincia" <?php if (isset($provinciaVal) && $provinciaVal=="") print " data-disabled='true' "  ?> name="campo[provincia]" id="provincia">
                                            <option value="">Seleccione</option>
                                            <?php
                                            $sum = 1;
                                            foreach ($info['provincias'] as $provincia) {
                                                ?>
                                                <option <?php
                                                if (isset($provinciaVal))
                                                    if ($sum == $provinciaVal) {
                                                        echo 'selected';
                                                    }
                                                    ?> value="<?php echo $sum; ?>"><?php echo $provincia->etiqueta ?></option>
                                                    <?php
                                                    $sum++;
                                                }
                                                ?>
                                            </select>
                                        </div>                            
                                        <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 noPAS">
                                            <label>Letras <span required="" aria-required="true">*</span></label>
                                            <select data-rule-required="true" class="form-control letra" name="campo[letra]" id="letra">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($info['letras'] as $letra) { ?>
                                                <option <?php
                                                if (isset($letraVal)) {
                                                    if ($letra->etiqueta == $letraVal) {
                                                        echo 'selected';
                                                    }
                                                }
                                                ?> value="<?php echo $letra->etiqueta ?>"><?php echo $letra->etiqueta ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>                            
                                        <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 noPAS">
                                            <label>Tomo <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php
                                            if (isset($tomo)) {
                                                echo $tomo;
                                            }
                                            ?>" type="text" id="tomo" name="campo[tomo]" class="form-control tomo">
                                        </div>
                                        <div class="  col-xs-6 col-sm-3 col-md-2 col-lg-2 noPAS">
                                            <label>Asiento <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php
                                            if (isset($asiento)) {
                                                echo $asiento;
                                            }
                                            ?>" type="text" id="asiento" name="campo[asiento]" class="form-control asiento">
                                        </div>
                                    </div>
                                    <div class="PAS">
                                        <div class="  col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label>No. Pasaporte <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php
                                            if (isset($pasaporte)) {
                                                echo $pasaporte;
                                            }
                                            ?>" type="text"  id="pasaporte" name="campo[pasaporte]" class="form-control pasaporte">
                                        </div>
                                    </div>

                                </div>
                                <p></p>
                                <div class="row">
                                    <div class="  col-xs-6 col-sm-2 col-md-2 col-lg-2 ">
                                        <label>Fecha de nacimiento</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="input-left-addon" name="campo[fecha_nacimiento]"
                                            value="<?php
                                            if (!empty($campos["datos"]->fecha_nacimiento)) {
                                                echo $campos["datos"]->fecha_nacimiento;
                                            }
                                            ?>" class="form-control datepicker" id="fecha_nacimiento">
                                        </div></div>
                                        <div class="form-group col-xs-6 col-sm-1 col-md-1 col-lg-1 " >
                                            <label>Edad</label>
                                            <input type="input-left-addon" name="edad"
                                            value="<?php
                                            if (!empty($campos["datos"]->edad)) {
                                             echo $campos["datos"]->edad;
                                         }
                                         ?>" class="form-control" id="edad" disable>
                                     </div>
                                     <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
                                        <label>Estado civil <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control estado_civil" name="campo[estado_civil]" id="estado_civil">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($estado_civil as $row) { ?>
                                            <option <?php
                                            if (!empty($campos["datos"]->estado_civil)) {
                                                if ($row->id_cat == $campos["datos"]->estado_civil) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $row->id_cat ?>"><?php echo $row->etiqueta ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 nombreAjustadores"><label>Nacionalidad</label>
                                        <input type="text" name="campo[nacionalidad]" id="nacionalidad" value="<?php
                                        if (!empty($campos["datos"]->nacionalidad)) {
                                            echo $campos["datos"]->nacionalidad;
                                        }
                                        ?>" class="form-control" id="campo[nacionalidad]">
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
                                        <label>Sexo <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control sexo" name="campo[sexo]" id="sexo">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($sexo as $se) { ?>
                                            <option <?php
                                            if (!empty($campos["datos"]->sexo)) {
                                                if ($se->id_cat == $campos["datos"]->sexo) {
                                                    echo 'selected';
                                                }
                                            }
                                            ?> value="<?php echo $se->id_cat ?>"><?php echo $se->etiqueta ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Estatura(mt)</label>
                                        <input type="text" name="campo[estatura]" id="estatura" value="<?php
                                        if (!empty($campos["datos"]->estatura)) {
                                            echo $campos["datos"]->estatura;
                                        }
                                        ?>" class="form-control" id="campo[estatura]" data-rule-required="true">
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 nombreAjustadores"><label>Peso(lb)</label>
                                        <input type="text" name="campo[peso]" id="peso" value="<?php
                                        if (!empty($campos["datos"]->peso)) {
                                            print $campos["datos"]->peso;
                                        }
                                        ?>" class="form-control"  data-rule-required="true">
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                        <div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
                                            <input type="checkbox" name="campo[telefono]" value="TR" class="" id="telefono_residencial_check" <?php if (isset($campos["datos"]->telefono_principal)) $campos['datos']->telefono_principal == 'Residencial' ? print 'checked' : '' ?>
                                            >
                                            <input type="hidden" name="campo[telefono_principal]" id="telefono_principal">
                                            <label class="checkbox" for="campo[telefono]"></label>
                                        </div>
                                        <label>Teléfono residencial</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span><input type="input-left-addon" name="campo[telefono_residencial]"
                                            value="<?php if (!empty($campos['datos']->telefono_residencial)) print $campos['datos']->telefono_residencial ?>" class="form-control telefono_residencial" id="telefono_residencial" 

                                            >
                                        </div></div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                            <div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
                                                <input type="checkbox" name="campo[telefono]" value="TL" <?php if (!empty($campos['datos']->telefono_principal)) print 'checked' ?>" class="" id="telefono_oficina_check"<?php if (isset($campos['datos']->telefono_principal)) $campos['datos']->telefono_principal == 'Laboral' ? print 'checked' : '' ?> >
                                                <label class="checkbox" for="campo[telefono]"></label>
                                            </div>
                                            <label>Teléfono oficina </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-phone"></i></span><input type="input-left-addon" name="campo[telefono_oficina]"
                                                value="<?php if (!empty($campos['datos']->telefono_oficina)) print $campos['datos']->telefono_oficina ?>" class="form-control telefono_oficina" id="telefono_oficina">
                                            </div></div>
                                        </div>
                                        <div class="row"> 
                                            <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 "><label>Direcci&oacute;n residencial</label>
                                                <div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
                                                    <input type="checkbox" name="campo[direccion]" value="DR" class="" id="direccion_residencial_check" <?php if (isset($campos["datos"]->direccion_principal)) $campos['datos']->direccion_principal == 'Residencial' ? print 'checked' : '' ?>
                                                    >
                                                    <label class="checkbox" for="campo[direccion_residencial_check]"></label>
                                                </div>
                                                <input type="input-left-addon" name="campo[direccion_residencial]"
                                                value="<?php
                                                if (!empty($campos["datos"]->direccion_residencial)) {
                                                 echo $campos["datos"]->direccion_residencial;
                                             }
                                             ?>" class="form-control" id="direccion">
                                             <label for="campo[direccion]" generated="true" class="error"></label>
                                         </div>

                                         <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 "><label>Direcci&oacute;n laboral</label>
                                            <div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
                                                <input type="checkbox" name="campo[direccion]" value="DL" class="" id="direccion_laboral_check" <?php if (isset($campos["datos"]->direccion_principal)) $campos['datos']->direccion_principal == 'Laboral' ? print 'checked' : '' ?>
                                                >
                                                <input type="hidden" name="campo[direccion_principal]" id="direccion_principal">
                                                <label class="checkbox" for="campo[direccion_laboral_check]"
                                                ></label>
                                            </div>
                                            <input type="input-left-addon" name="campo[direccion_laboral]"
                                            value="<?php
                                            if (!empty($campos["datos"]->direccion_laboral)) {
                                             echo $campos["datos"]->direccion_laboral;
                                         }
                                         ?>" class="form-control" id="direccion_laboral">
                                         <label for="campo[direccion_laboral]" generated="true" class="error"></label>
                                     </div>
                                     <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 ">
                                        <label>Correo electrónico </label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span><input type="email" name="campo[correo]"
                                            value="<?php if (!empty($campos['datos']->correo)) print $campos['datos']->correo ?>" class="form-control correo" id="correoPersona">
                                        </div></div>
                                    </div>
                                    <div class="row" id="observa">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 "><label>Observaciones</label>
                                            <textarea name="campo[observaciones]"
                                            value="" class="form-control" id="observacionesPersona"><?php
                                            if (!empty($campos["datos"]->observaciones)): echo $campos["datos"]->observaciones;
                                            endif;
                                            ?></textarea>
                                            <label for="campo[observaciones]" generated="true" class="error"></label>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-4 divestado">

                                            <label>Estado </label>
                                            <select class="form-control estado_carga" id="estadoPersona" name="campo[estado]"  data-rule-required="true">

                                                <?php
                                                $activo = "";
                                                $inactivo = "";
                                                if (isset($campos['estado']) && $campos['estado'] == "Activo")
                                                    $activo = "selected";
                                                if (isset($campos['estado']) && $campos['estado'] == "Inactivo")
                                                    $inactivo = "selected";
                                              if (isset($campos['estado'])) {
											
											if($campos['superadmin']==0)
											{
												if (count($campos['politicas_generales'] > 0)) {
													if ($campos['estado'] == "Activo") {
														if (((in_array(19, $campos['politicas']) === true) && (in_array(19, $campos['politicas_generales']) === true)) || ((in_array(19, $campos['politicas_generales']) === false)) ) {
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
														
														if (((in_array(20, $campos['politicas']) === true) && (in_array(20, $campos['politicas_generales']) === true)) || ((in_array(20, $campos['politicas_generales']) === false)) ) {
															?>
															<option value='Activo' <?php echo $activo ?> >Activo</option>
															<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
															<?php
															}
															else
															{
															?>
														  <option value='Inactivo' <?php  echo $inactivo ?> >Inactivo</option>
															<?php
														}
														
													}
												} else {
													?>
													<option value='Activo' >Activo</option>
													<option value='Inactivo' >Inactivo</option>
													<?php
												}
											}
											else {
													?>
													<option value='Activo' >Activo</option>
													<option value='Inactivo' >Inactivo</option>
													<?php
												}
                                        } else {
                                            ?>
                                            <option value='Activo' <?php echo $activo ?> >Activo</option>
                                            <?php
                                        }
                                                ?>
                                            </select>                                
                                        </div>

                                        <?php if (!isset($campos['uuid'])): ?>

                                            <div class="row" id="doc_entregados">
                                                <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 div2_persona documentos_entregados" >
                                                    <p></p>
                                                    <h5>Documentos entregados</h5><br><br>
                                                    <label>Nombre del documento</label>
                                                    <div class='file_upload_persona' id='fpersona1'>
                                                        <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                                        <br><br>
                                                    </div>
                                                    <div id='file_tools_persona' style="width: 90px!important; float: left;">
                                                        <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_persona"><i class="fa fa-plus"></i>
                                                        </button>
                                                        <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_persona"><i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="espac">
                                                <br><br>  
                                            </div>

                                        <?php endif ?>  
                                <!--<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-6" >
                            </div>-->

                        </div>

                        <div class="row detalleinteres_persona" style="display:none">
                            <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                            <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12" >
                                <h5>Detalle interés asegurado</h5>
                                <br>
                                <hr>
                                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 persona">
                                    <label>Relación</label>
                                    <select name="campodetalle[relacion]" id="relaciondetalle_persona" class="form-control relaciondetalle_persona_vida_otros" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <option value="Principal">Principal</option>
                                        <option value="Dependiente" disabled>Dependiente</option>
                                    </select>

                                    <select name="campodetalle[relacion_benficario]" id="relaciondetalle_persona" class="form-control relaciondetalle_persona_vida hidden" data-rule-required="true" disabled>
                                        <option value="">Seleccione</option>
                                        <option value="Principal">Principal</option>
                                        <option value="Beneficiario" disabled>Beneficiario</option>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 persona">
                                    <div class="row">
                                        <label>Interes asegurado asociado</label>
                                    </div>
                                    <div class="row">
                                        <select name="campodetalle[interes_asociado]" id="asociadodetalle_persona" disabled class="form-control" 
                                        style="width: 100%">
                                        <option value="">Seleccione</option>
                                        <option v-for="interaso in InteresesAsociados" v-bind:value="interaso.id">{{ "N°"+interaso.detalle_certificado+"-"+interaso.nombrePersona}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 salud">
                                <label>Tipo de relación</label>
                                <select name="campodetalle[tipo_relacion]" id="tipo_relacion_persona" class="form-control" disabled data-rule-required="true">
                                    <option value="">Seleccione</option>
                                    <option value="Conyugue">Conyugue</option>
                                    <option value="Hijo(a)">Hijo(a)</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-1">
                                <label>N° Certificado</label>
                                <div class="input-group">
                                    <input type="text" name="campodetalle[certificado]" id="certificadoPersona" class="form-control">
                                </div>                                      
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-1 salud">
                                <label>Beneficio vida</label>
                                <select name="campodetalle[beneficio_vida]" id="beneficiodetalle_persona" class="form-control beneficiodetalle_persona">
                                    <option value="">Seleccione</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 montoPersona salud">
                                <label>Monto</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" name="campodetalle[monto]" id="montodetalle_persona" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2  vida">
                                <label>Suma asegurada</label>
                                <div class="input-group">
                                    <span class="input-group-addon" >$</span>
                                    <input type="text" name="campodetalle[suma_asegurada]" id="suma_asegurada_persona" class="form-control" required="">
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2  vida persona">
                                <label>Participación</label>
                                <div class="input-group">
                                    <span class="input-group-addon">%</span>
                                    <input type="text" name="campodetalle[participacion]" id="participacion_persona" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                <label id="primaNeta">Prima neta<span required="" aria-required="true">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" name="campodetalle[prima_anual]" id="primadetalle_persona" class="form-control">
                                </div>                                      
                            </div>

                        </div>
                    </div>

                    <br>
                    <!-- Acreedores Para Vida Colectivo -->
                    <div class="row ibox-content" id="vigencia_vida_colectivo" style="display: none">
                        <div id="campos_vida_acreedor_crear">
                            <div class="row" id="divacreedores">
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                                    <label class="nombre_doc_titulo" id="nombre_acre_titulo">Acreedor</label>
                                </div>    
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_cesion">% Cesión</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_monto">Monto</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_inicio">Fecha Inicio</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_fin">Fecha Fin</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" ></label>
                                </div>        
                            </div>
                            <div style="margin-bottom: 25px;" class="">
                                <div class="file_tools_acreedores_adicionales row" id="a1">
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                                        <input type="text" name="campoacreedores[]" id="acreedor_1" class="form-control">
                                    </div>    
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">%</span> 
                                            <input type="text" name="campoacreedores_por[]" id="porcentajecesion_1" class="form-control porcentaje_cesion_acreedor" value="0">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span> 
                                            <input type="text" name="campoacreedores_mon[]" id="montocesion_1" class="form-control monto_cesion_acreedor" value="0">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="campoacreedores_ini[]" id="fechainicio_1" class="form-control fechas_acreedores_inicio">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="campoacreedores_fin[]" id="fechafin_1" class="form-control fechas_acreedores_fin">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" style="float: left; width: 40px; margin-right:5px;" onclick="agregaracre()"><i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" style="float: left; width: 40px; margin-top:0px!important; display: none" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaracre(1)"><i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div> 
                                <div id="agrega_acre"></div>           
                            </div>
                        </div> 
                        <div id="campos_vida_acreedor_editar">
                            <div class="row" id="divacreedores">
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                                    <label class="nombre_doc_titulo" id="nombre_acre_titulo">Acreedor</label>
                                </div>    
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_cesion">% Cesión</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_monto">Monto</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_inicio">Fecha Inicio</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" id="nombre_acre_fin">Fecha Fin</label>
                                </div> 
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label class="nombre_doc_titulo" ></label>
                                </div>        
                            </div>

                            <div style="margin-bottom: 25px;" class="">
                                <div class="file_tools_acreedores_adicionales row" v-for="find in acreedores" track-by="$index" id="a{{$index + 2}}">
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                                        <input type="text" name="campoacreedores[]" id="acreedor_{{$index + 2}}" class="form-control" value="{{find.acreedor}}">
                                    </div>    
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">%</span> 
                                            <input type="text" name="campoacreedores_por[]" id="porcentajecesion_{{$index + 2}}" class="form-control porcentaje_cesion_acreedor" value="{{find.porcentaje_cesion}}">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span> 
                                            <input type="text" name="campoacreedores_mon[]" id="montocesion_{{$index + 2}}" class="form-control monto_cesion_acreedor" value="{{find.monto_cesion}}">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="campoacreedores_ini[]" id="fechainicio_{{$index + 2}}" class="form-control fechas_acreedores_inicio" value="{{find.fecha_inicio}}">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="campoacreedores_fin[]" id="fechafin_{{$index + 2}}" class="form-control fechas_acreedores_fin" value="{{find.fecha_fin}}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <!--<button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i>
                                        </button>-->
                                        <button type="button" style="float: left; width: 40px; margin-top:0px!important; display: block !important" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaacreedor({{$index+2}})"><i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="campoacreedores_id[]" value="{{find.id}}">
                                </div>

                                <div class="file_tools_acreedores_adicionales row" id="a1">
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                                        <input type="text" name="campoacreedores[]" id="acreedor_1" class="form-control">
                                    </div>    
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">%</span> 
                                            <input type="text" name="campoacreedores_por[]" id="porcentajecesion_1" class="form-control porcentaje_cesion_acreedor" value="0">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span> 
                                            <input type="text" name="campoacreedores_mon[]" id="montocesion_1" class="form-control monto_cesion_acreedor" value="0">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="campoacreedores_ini[]" id="fechainicio_1" class="form-control fechas_acreedores_inicio">
                                        </div>
                                    </div> 
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="campoacreedores_fin[]" id="fechafin_1" class="form-control fechas_acreedores_fin">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" onclick="agregaracre()" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" style="float: left; width: 40px; margin-top:0px!important;" id="del_acre" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaracre(1)"><i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="campoacreedores_id[]" value="0">
                                </div>

                                <div id="agrega_acre"></div> 
                            </div>

                        </div>   
                    </div>
                    <!-- Fin Acreedores Para Vida Colectivo -->

                    <input type="hidden" name="campodesde[desde]" class="campodesde" value="">
                    <input type="hidden" name="campodesde[indcolec]" class="indcolec" value="">
                    <input type="hidden" name="campodetalle[personaInvidual]" id="personaInvidual" value="">

                    <div class="row botones">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                        <?php 
                            $url = 'intereses_asegurados/listar';
                            if (!empty($_GET['reg']) && $_GET['reg'] == "poli" ){
                                $url = "polizas/editar/".$_GET['val'];
                            }
                        ?>
                        <a
                            href="<?php echo base_url($url); ?>" class="btn btn-default btn-block"
                            id="cancelar">Cancelar </a></div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                <input type="submit" name="campo[guardar]" value="Guardar "
                                class="btn btn-primary btn-block guardarPersona guardarInteresSolicitud" id="campo[guardar]">
                            </div>
                        </div>
                        <div class="tabladetalle_personas" style="display:none">
                            <!-- JQGRID -->
                            <?php echo modules::run('intereses_asegurados/ocultotablapersonas', $campos); ?>
                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div> 
</div>


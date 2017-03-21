<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>


        <div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">
                    <div id="mensaje">
                    </div>
                </div>
                <div ng-controller="toastController"></div>

                <div class="ibox">
                    <!-- Tab panes -->
                    <div class="ibox-content" >

                    <?php
                        $formAttr = array(
                            'method' => 'POST',
                            'id' => 'crearRemesaForm',
                            'autocomplete' => 'off'
                        );
                        echo form_open(base_url(uri_string()), $formAttr);
                    ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                    <label>Agentes</label>
                                    <select name="agente" id="agente" class="form-control" v-model='agente' @change="getInfoAgente(agente)">
                                        <option value="">Seleccione</option>
                                        <?php
                                            foreach ($agentes as $key => $value) {
                                                echo '
                                                <option value="'.$value->id.'">'.$value->nombre.'</option>
                                                ';
                                            }
                                        ?>
                                    </select>
                                </div>
								<div class="noPAS row">
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
										<label>Identificación <span required="" aria-required="true"></span></label>
										<input type="text" id="tipo_natural" name="tipo_natural" class="form-control" disabled value="Natural">
									</div>  
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
										<label>Provincia <span required="" aria-required="true"></span></label>
										<input type="text" id="provincia_natural" name="provincia_natural" class="form-control" disabled>
									</div>                            
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-1" >
										<label>Letras <span required="" aria-required="true"></span></label>
										<input type="text" id="letra_natural" name="letra_natural" class="form-control" disabled>
									</div>                           
									<div class="form-group col-xs-6 col-sm-3 col-md-1 col-lg-1">
										<label>Tomo <span required="" aria-required="true"></span></label>
										<input type="text" id="tomo_natural" name="tomo_natural" class="form-control" disabled>
									</div>
									<div class="form-group col-xs-6 col-sm-3 col-md-1 col-lg-2">
										<label>Asiento <span required="" aria-required="true"></span></label>
										<input type="text" id="asiento_natural" name="asiento_natural" class="form-control" disabled>
									</div>
								</div>
								<div class="RUC">
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
										<label>Identificación <span required="" aria-required="true"></span></label>
										<input type="text" id="tipo_natural" name="tipo_natural" class="form-control" disabled value="Jurídico">
									</div>
									<div class="form-group col-xs-12 col-sm-3 col-md-1 col-lg-1">
										<label>Tomo <span required="" aria-required="true"></span></label>
										<input type="text" id="tomo_ruc" name="tomo_ruc" class="form-control" disabled>
									</div>
									<div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
										<label>Folio/Imagen/Documento <span required="" aria-required="true"></span></label>
										<input type="text" id="folio_ruc" name="folio_ruc" class="form-control" disabled>
									</div>
									<div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
										<label>Asiento/Ficha <span required="" aria-required="true"></span></label>
										<input type="text" id="asiento_ruc" name="asiento_ruc" class="form-control" disabled>
									</div>
									<div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
										<label>Digito Verificador <span required="" aria-required="true"></span></label>
										<input type="text" id="digito_ruc" name="digito_ruc" class="form-control" disabled>
									</div>    
								</div>

								<div class="PAS">
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
										<label>Identificación <span required="" aria-required="true"></span></label>
										<input type="text" id="tipo_natural" name="tipo_natural" class="form-control" disabled value="Pasaporte">
									</div>
									<div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-3">
										<label>No. Pasaporte <span required="" aria-required="true">*</span></label>
										<input type="text"  id="pasaporte" name="pasaporte" class="form-control" disabled>
									</div>
								</div>
                            </div>
                        </div>
						<div class="row">
							 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 ">
									<label>Teléfono </label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-phone"></i></span>
										<input type="input-left-addon" name="telefono" class="form-control" data-inputmask="'mask': '999-9999', 'greedy':true" id="telefono" disabled> </div>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 ">
									<label>Correo Electrónico</label>
									<div class="input-group">
										<span class="input-group-addon">@</span><input type="input-left-addon" name="correo" data-rule-email="true" class="form-control debito" id="correo" disabled></div>
									<label for="campo[correo]" generated="true" class="error"></label>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                    <label>Rango de fechas</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                                        <input type="input" id="fecha_desde" name="fecha_desde" readonly="readonly" class="form-control" value="" data-rule-required="true">
                                        <span class="input-group-addon">a</span>
                                        <input type="input" id="fecha_hasta" name="fecha_hasta" readonly="readonly" class="form-control" value="" data-rule-required="true">
                                    </div>
                                </div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 " id='no_pago_div'>
									<label>N. Pago <span required="" aria-required="true"></span></label>
									<input type="text" id="no_pago" name="no_pago" class="form-control" disabled>
								</div>
							</div>
						</div>
                        <div class="row" id='botones_editar'>
							<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
							<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
								<input type="button" id="clearBtn" class="btn btn-success btn-block" value="Limpiar" @click="limpiarCamposHonorarios()" />
                            </div>
							<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
								<input type="button" id="actualizar" class="btn btn-success btn-block" value="Actualizar" @click="getHonorarios()" />
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                    </div>
                </div>

                <?php  echo modules::run('honorarios_seguros/tabla_comisiones')?>

            </div>

            

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
?>
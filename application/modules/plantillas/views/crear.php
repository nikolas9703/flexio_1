<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="formulario-plantilla">

                <div class="row filtro-plantillas hide">
                    <?php
                    $formAttr = array(
                        'method' => 'POST',
                        'id' => 'plantillaForm',
                        'autocomplete' => 'off',
                        'class' => 'form-inline hide'
                    );
                    echo form_open(base_url(uri_string()), $formAttr);
                    ?>
                    <div class="form-group-no-margin col-xs-12 col-sm-12 col-md-3 col-lg-2">
                        <label class="">Plantilla</label>

                        <?php if (count($plantilla_inf) > 0): ?>
                            <select id="plantilla_id" class="white-bg hide" v-chosen="plantilla_id_selected" disabled="disabled">
                                <template v-for="optgroups in plantillaOptions" track-by="$index">
                                    <option v-for="plantilla_ver in plantilla_verOptions" value="{{plantilla_ver.plantilla_id}}">{{{plantilla_ver.plantilla_nombre}}}</option>
                                <?php else: ?>
                                    <select id="plantilla_id" class="white-bg hide" v-chosen="plantilla_id_selected">
                                        <template v-for="optgroups in plantillaOptions" track-by="$index">
                                            <optgroup v-if="optgroups.options" label="{{optgroups.options ? optgroups.label : ''}}">
                                                <option v-for="option in optgroups.options" v-bind:value="option.id" selected="{{option.id == plantilla_id_selected}}" data-tab-id="{{{option.nav_tab_id}}}" data-toggle="tab">{{{option.nombre}}}</option>
                                            </optgroup>
                                            <option v-if="optgroups.value" v-bind:value="optgroups.value" selected="{{optgroups.value == plantilla_id_selected}}" data-tab-id="{{{optgroups.nav_tab_id}}}" data-toggle="tab"><strong>{{{optgroups.label}}}</strong></option>
                                        <?php endif; ?>	
                                    </template>
                                </select>
                                </div>
                                <div class="form-group-no-margin col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Colaborador <span class="required">*</span></label>
                                    <select id="colaborador_id" class="white-bg hide" v-chosen="colaborador_id_selected" data-rule-required="true" <?php count($plantilla_inf) > 0 ? print('disabled') : '' ?>>
                                        <option value="">Seleccione</option>
                                        <template v-for="colaborador in colaboradoresOptions" track-by="$index">
                                            <?php if (count($plantilla_inf) > 0): ?>
                                                <option v-for="plantilla_ver in plantilla_verOptions" v-bind:value="colaborador.id" selected="{{colaborador.id == plantilla_ver.colaborador_id}}">{{{colaborador.nombre}}}</option>
                                            <?php else: ?>
                                                <option selected="{{colaborador.id==colaborador_id_selected}}" v-bind:value="colaborador.id">{{{colaborador.nombre}}}</option>
                                            <?php endif; ?>	
                                        </template>
                                    </select>
                                </div>
                                <div class="form-group-no-margin col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Destinatario <span class="required">*</span></label>
                                    <select id="destinatario_id" class="white-bg hide" v-chosen="destinatario_id_selected" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php if (count($plantilla_inf) > 0): ?>
                                            
                                                <option value="aquien_concierne" v-for="plantilla_ver in plantilla_verOptions" v-bind:value="plantilla_ver.acreedor" selected="{{plantilla_ver.acreedor == 'aquien_concierne' }}">A quien Concierne</option>
                                           
                                            <template v-for="acreedor in acreedoresOptions" track-by="$index">
                                                <option v-for="plantilla_ver in plantilla_verOptions" v-bind:value="acreedor.id" selected="{{acreedor.id == plantilla_ver.acreedor}}">{{{acreedor.nombre}}}</option>
                                            </template>
                                        <?php else: ?>
                                            <option value="aquien_concierne">A quien Concierne</option>
                                            <template v-for="acreedor in acreedoresOptions" track-by="$index">
                                                <option v-bind:value="acreedor.id">{{{acreedor.nombre}}}</option>
                                            </template>
                                        <?php endif; ?>
                                    </select>
                                </div> 
                                <div class="form-group-no-margin col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Prefijo</label>
                                    <select id="prefijo_id" class="white-bg hide" v-chosen="prefijo_id_selected" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php if (count($plantilla_inf) > 0): ?>
                                            <template v-for="prefijo in prefijosOptions" track-by="$index">
                                                <option v-for="plantilla_ver in plantilla_verOptions" v-bind:value="prefijo.id" selected="{{prefijo.id == plantilla_ver.prefijo_id}}">{{{prefijo.nombre}}}</option>
                                            </template>
                                        <?php else: ?>
                                            <template v-for="prefijo in prefijosOptions" track-by="$index">
                                                <option v-bind:value="prefijo.id">{{{prefijo.nombre}}}</option>
                                            </template>
                                        <?php endif; ?>
                                    </select>
                                </div> 
                                <div class="form-group-no-margin col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Firmado por <span class="required">*</span></label>
                                    <select id="firmado_por" class="white-bg hide" v-chosen="firmado_por_id_selected" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php if (count($plantilla_inf) > 0): ?>
                                            <template v-for="firmado_por in firmado_porOptions" track-by="$index">
                                                <option v-for="plantilla_ver in plantilla_verOptions" v-bind:value="firmado_por.id" selected="{{firmado_por.id == plantilla_ver.firmado_por}}">{{{firmado_por.nombre}}}</option>
                                            </template>
                                        <?php else: ?>
                                            <template v-for="firmado_por in firmado_porOptions" track-by="$index">
                                                <option v-bind:value="firmado_por.id">{{{firmado_por.nombre}}}</option>
                                            </template>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <!-- Hide Nav-Tabs -->
                                <ul class="nav nav-tabs hide">
                                    <template v-for="optgroups in plantillaOptions" track-by="$index">
                                        <li v-if="optgroups.options" v-for="option in optgroups.options"><a href="#{{{option.nav_tab_id}}}Tab" data-toggle="tab">{{{option.nombre}}}</a></li>
                                        <li v-if="optgroups.value"><a href="#{{optgroups.nav_tab_id}}Tab" data-toggle="tab">{{{optgroups.label}}}</a></li>
                                    </template>
                                </ul>
                                <?php echo form_close(); ?>
                                </div>
                                <div class="row">	
                                    <!-- Tabs Content Plantillas -->
                                    <div class="tab-content filtro-plantillas-content m-t-sm">
                                       <?php 
                                                          
                                       if(count($plantilla_plantilla)>0){
                                          
                                          echo '<textarea id="inline-ckeditor-'.$plantilla_inf[0]['plantilla_id'] .'" name="inline-ckeditor" class="inline-ckeditor form-control">'.$plantilla_plantilla.'</textarea>'; 
                                       }else{

						foreach($grupo_plantillas AS $grupo => $plantillas){
							if(count($plantillas)>1){
								foreach($plantillas AS $plantilla){
									$tab_id = str_replace(" ", "", ucWords(str_replace("Adendas", "adenda", str_replace("Cartas", "carta", $grupo)) ." ". $plantilla["nombre"]));
									echo '<div class="tab-pane" id="'. $tab_id .'Tab">'. modules::run('plantillas/plantilla', array("plantilla_id" => $plantilla["id"])) .'</div>';
								}
							}else{
								$tab_id = str_replace("&oacute;", "o", str_replace(" ", "", ucWords($grupo)));
								echo '<div class="tab-pane" id="'. $tab_id .'Tab">'. modules::run('plantillas/plantilla', array("plantilla_id" => $plantillas[0]["id"])) .'</div>';
							}
						}
                                       }
						?>

					</div>
					
					<div class="row m-t-sm hide {{displayBtn}}">
	                    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
	                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="javascript:history.back(1)" class="btn btn-default btn-block">Cancelar</a></div>
	                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
	                        <button type="button" id="vistaPreviaBtn" class="btn btn-primary btn-block" v-on:click="vistaPrevia" v-html="vistaPreviaBtn">Vista Previa</button>
	                    </div>
	                </div>
	                  
                </div>
                   
                
                <?php 
				//Modal
				echo Modal::config(array(
					"id" => "vistaPreviaModal",
					"size" => "lg",
					"attr" => array(
						"class" => "inmodal"
					),
					"footer" => '<div class="row">
                                             
					   <div class="form-group col-xs-12 col-sm-6 col-md-6">
					<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
				</div>
				<div class="form-group col-xs-12 col-sm-6 col-md-6">
					<button id="guardarPlantillaBtn" v-on:click="guardarPlantilla" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button>
				</div>
				</div>'
                                ))->html();
                                ?>

                                </div>

            <div>
            <?php echo modules::run('plantillas/ocultoformulariocomentarios'); ?>
            </div>
                                </div><!-- cierra .col-lg-12 -->
                                </div><!-- cierra #page-wrapper --> 
                                </div><!-- cierra #wrapper -->
                                <div id="ocultohtml" style="width:720px; color:black!important; display:none; font-weight:600; padding-left:35px; padding-right:35px; font-family:arial!important;"></div>
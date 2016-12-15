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

               <?php
				$formAttr = array(
				    'method' => 'POST',
				    'id' => 'vue-colaboradores-talleres',
				    'autocomplete' => 'off',
					'class' => 'hide'
				);
				echo form_open(base_url('talleres/guardar'), $formAttr);
				?>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Datos del equipo de trabajo</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content" style="display:block;">
                        <div class="row">

					        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
								 <label>Nombre del equipo <span required="" aria-required="true">*</span></label>
					             <input v-model="nombre_equipo" type="text" name="campo[nombre]" class="form-control" data-rule-required="true" />
							</div>
							<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
								<label>Capacidad de &oacute;rdenes a atender</label>
					            <input v-model="ordenes_atender" type="text" name="campo[ordenes_atender]" class="form-control" id="ordenes_atender" data-rule-required="true" />
							</div>
							<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
								<label>Estado</label>
				                <select name="campo[estado_id]" id="estado_id" v-model="estado_id" class="form-control">
									<option value="">Seleccione</option>
									<template v-for="option in estadosOptions" track-by="$index" >
	                                 	<option v-bind:value="option.id">{{{option.nombre}}}</option>
	                            	</template>
								</select>
							</div>

                        </div>
                        <?php if(!empty($equipo_id)): ?>

                         <div class="row m-t-sm white-bg">
							<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
							<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
					        	<a href="<?php echo base_url('talleres/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
					        </div>
					        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
					            <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="guardar">
					        </div>
						</div>
						<?php endif; ?>
                    </div>
                </div>

                <?php if(empty($equipo_id)): ?>
                <div class="panel-group">
			<!--		<div class="panel panel-white">
						<div class="panel-heading">
							<h5 class="panel-title">
								<a href="#collapseOne" data-parent="#accordion" data-toggle="collapse" aria-expanded="true" class="centroAccordion" @click="verificarSeleccion('centro')">
									<input type="checkbox" class="js-switch" href="#collapseOne" id='chkcentro' :checked="centrosCheck ? 'checked' : null">
								</a>
				      			Centro contable
							</h5>
						</div>
						<div class="panel-collapse collapse {{centrosCheck == true ? 'in' : ''}}" id="collapseOne" aria-expanded="true" style="">

							<select id="centro_contable_id"  name="centro_contable_id[]" v-model="centrosSeleccionados"  multiple="multiple" class="form-control treeMultiselectField">
								<template v-for="option in centrosOptions" track-by="$index" >
                                 	<option value="{{option.value}}" data-section="{{option.data_section}}" data-index="{{option.data_index}}">{{{option.texto}}}</option>
                            	</template>
							</select>
							<div class="row m-t-sm">
								<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
						        	<a href="<?php echo base_url('talleres/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
						        </div>
						        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
						            <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]">
						        </div>
							</div>
 						</div>
					</div>-->

					<div class="panel panel-white">
						<div class="panel-heading">
							<h5 class="panel-title">
						<!--	<a href="#collapseTwo" data-parent="#accordion" data-toggle="collapse" aria-expanded="true" class="colaboradorAccordion" @click="verificarSeleccion('colaborador')">
								<input type="checkbox" class="js-switch" id='chkcolaboradores' :checked="colaboradorCheck ? 'checked' : null">
							</a>-->
							Colaboradores
							</h5>
						</div>
						<div class="panel-collapse collapse in" id="collapseTwo" aria-expanded="true" style="">
							<!-- Panel Content -->
							<div class="row">

								<agregar_colaboradores v-ref:colaboradores></agregar_colaboradores>
 							</div>
							<br>
							<div class="row m-t-sm">
								<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
						        	<a href="<?php echo base_url('talleres/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
						        </div>
						        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
						            <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]">
						        </div>
							</div>
							<!-- /Panel Content -->
						</div>
					</div>
				</div>
				<?php endif; ?>

                <input v-model="equipo_id" type="hidden" name="id" />
                <?php
				//cargar templates vue
				echo modules::run('talleres/vue_cargar_templates');



if(preg_match("/ver/i", $_SERVER['REQUEST_URI'])){

				//inicializar modal
				echo Modal::config ( array (
					"id" => "agregarColaboradoresModal",
					"size" => "md",
					"titulo" => "Agregar colaboradores",
					"contenido" => '<agregar_colaboradores v-ref:colaboradores></agregar_colaboradores>',
					"footer" => '<a class="btn btn-w-m btn-success" @click="agregarColaboradores($event)">Agregar</a>',
				))->html();
				echo form_close();
        }
				?>

                <div class="<?php echo empty($equipo_id) ? 'hide' : '' ?>">
	                <?php Subpanel::visualizar_grupo_subpanel($equipo_id); ?>
					<br/><br/>
					<?php echo modules::run('talleres/ocultoformulariocomentarios'); ?>
	            </div>
            </div>
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();

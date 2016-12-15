<div id="wrapper">
<?php
Template::cargar_vista('sidebar');
?>
<div id="page-wrapper" class="gray-bg row">

<?php Template::cargar_vista('navbar'); ?>
<div class="row border-bottom"></div>
<?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

<div class="col-lg-12">
	<div class="wrapper-content" id="form_usuarios_div">

		<div class="ibox">
			<div class="ibox-title border-bottom">
				<h5>{{detalle.id != '' ? 'Editar' : 'Crear'}} usuario</h5>
				<div class="ibox-tools">
					<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
				</div>
			</div>
			<div class="ibox-content m-b-sm">
		        <?php
		        $formAttr = array(
		        	"method"        => "POST",
		        	"id"            => "crearUsuarioForm",
		        	"autocomplete"  => "off"
		        );
		        echo form_open(base_url(uri_string()), $formAttr);
		        ?>
		        <div class="alert alert-warning" v-if="catalogos.roles.length == 0">
					No puede crear usuario para la empresa seleccionada, no cuenta con roles creados.
                    <a href="<?php echo base_url('roles/listar')?>" class="link"> (Ver Roles)</a>
                </div>

		        <div class="row"><p class="text-danger pull-right m-r">Todos los campos son obligatorios</p></div>
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label>Nombre</label>
							<input type="text" name="campo[nombre]" class="form-control" data-rule-required="true" v-model="detalle.nombre" />
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Apellido</label>
							<input type="text" name="campo[apellido]" class="form-control" data-rule-required="true" v-model="detalle.apellido" />
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Correo Electr&oacute;nico</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
								<input type="text" name="campo[email]" class="form-control" data-rule-required="true" data-rule-email="true" v-model="detalle.email" :disabled="detalle.id != ''"/>
							</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Accesso al Sistema</label>
							<select name="campo[rol][]" class="form-control" data-rule-required="true" v-select2="detalle.rol" :config="config.select2">
								<option value="">Seleccione</option>
								<option :value="acceso.id" v-for="acceso in catalogos.rol" v-html="acceso.nombre"></option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Contrase&ntilde;a</label>
							<input type="password" name="campo[password]" id="password" class="form-control" data-rule-required="{{detalle.id == '' ? 'true' : ''}}" data-rule-minlength="5" v-model="detalle.password" />
							<small>La contrase&ntilde;a debe ser de 5 caracteres minimo.</small>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Repetir Contrase&ntilde;a</label>
							<input type="password" name="campo[repetir_password]" class="form-control" data-rule-required="{{detalle.id == '' ? 'true' : ''}}" data-rule-equalto="#password" v-model="detalle.repetir_password" />
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Rol</label>
							<select name="campo[rol][]" class="form-control" data-rule-required="true" v-select2="detalle.roles" :config="config.select2">
								<option value="">Seleccione</option>
								<option :value="rol.id" v-for="rol in catalogos.roles" v-html="rol.nombre"></option>
							</select>
						</div>
					</div>

					<div class="col-sm-3">
						<div class="form-group">
							<label>Centro(s) Contable(s)</label>
							<select name="campo[centros_contables]" multiple="true" class="form-control" v-select2="detalle.centros_contables" :config="config.select2">
								<option value="todos">Todos</option>
								<option :value="centro_contable.centro_contable_id" v-for="centro_contable in getCentrosContables" v-html="centro_contable.nombre"></option>
							</select>
						</div>
					</div>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8"></div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        	<input type="button" class="btn btn-w-m btn-default pull-right btn-block" value="Cancelar" @click="clearForm()" />
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 pull-right">
                            <button class="btn btn-w-m btn-success btn-block" name="guardarBtn" type="button" @click="guardar($event)" :disabled="catalogos.roles.length == 0 || config.disableDetalle">Guardar</button>
                        </div>
                    </div>
                </div>

				<input type="hidden" name="campo[id]" v-model="detalle.id" value="" />
		        <?php echo form_close(); ?>
	        </div>
        </div>

		<listar-usuarios :config.sync="config" :catalogos="catalogos" :detalle.sync="detalle"></listar-usuarios>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

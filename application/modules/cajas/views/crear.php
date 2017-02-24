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
            			<div ng-controller="toastController"></div>
			    <?php
                $formAttr = array(
                    'method'        => 'POST',
                    'id'            => 'cajasForm',
                    'autocomplete'  => 'off',
					'enctype'		=> 'multipart/form-data',
					'ng-controller'	=> 'FormularioCajaController'
                );
				echo form_open(base_url(uri_string()), $formAttr);
                ?>
		        <div class="ibox">
		            <div class="ibox-title border-bottom">
		                <h5><?php echo !empty($caja_uuid) ? "Consulta de caja" : "Crear caja"; ?></h5>
		                <div class="ibox-tools">
		                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
		                </div>
		            </div>
		            <div style="display: block; border:0px" class="ibox-content m-b-sm">
		                <div class="row">

		                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre ">
		                        <label>Nombre <span class="required">*</span></label>
		                        <input type="text" id="nombre" class="chosen-select form-control" value="" name="nombre" ng-model="caja.nombre" data-rule-required="true" data-msg-required="">
		                    </div>
		                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
		                        <label>Centro contable<span class="required">*</span></label>
		                        <select id="centro_contable_id" class="chosen-select form-control chosen-select" name="centro_contable_id" ng-model="caja.centro_contable_id" data-rule-required="true" data-msg-required="">
		                        	<option value="">Seleccione</option>
				                    <option ng-repeat="centro_contable in centroContableList track by $index" value="{{centro_contable.centro_contable_id && '' || centro_contable.centro_contable_id}}">{{centro_contable.nombre && '' || centro_contable.nombre}}</option>
		                        </select>
		                    </div>
		                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
		                        <label>Responsable </label>
		                        <select id="responsable_id" class="chosen-select form-control chosen-select" name="responsable_id" ng-model="caja.responsable_id" ng-disabled="caja.id" />
		                        	<option value="">Seleccione</option>
				                    <option ng-repeat="usuario in usuariosList track by $index" value="{{usuario.id && '' || usuario.id}}">{{usuario.nombre_completo && '' || usuario.nombre_completo}}</option>
		                        </select>
		                    </div>
		                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
		                        <label>L&iacute;mite <span class="required">*</span></label>
		                        <div class="input-group">
		                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
		                            <input type="text" id="limite" class="form-control limite" value="" name="limite" ng-model="caja.limite" data-rule-required="true" data-msg-required=""/>
		                        </div>
		                    </div>
		                </div>

		                <!-- Balance -->
		                <?php if(!empty($caja_uuid)): ?>
		                <div class="row">
		                	<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre ">
		                        <label>&nbsp;</label>
		                        <div class="form-control" style="border:0;">Balance actual:</div>
		                    </div>
		                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
		                        <label>&nbsp;</label>
		                        <div class="input-group">
		                            <span class="input-group-addon">$</span>
		                            <input type="text" id="saldo" class="form-control" value="" name="saldo" disabled="disabled" ng-model="caja.saldo" />
		                        </div>
		                        <label class="label label-danger m-t-xs p-xs col-xs-12 col-sm-12 col-md-12 col-lg-12">saldo</label>
		                    </div>
		                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
		                        <label>&nbsp;</label>
		                        <div class="input-group">
		                            <span class="input-group-addon">$</span>
		                            <input type="text" id="maxportransferir" class="form-control" value="" name="maxportransferir" ng-model="caja.maxportransferir" disabled="disabled" />
		                        </div>
		                        <label class="label m-t-xs p-xs col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background:#5cb85c;color:#fff;">Max. por transferir</label>
		                    </div>
		                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
		                        <label>Estado</label>
		                        <select id="estado_id" class="chosen-select form-control" name="estado_id" ng-model="caja.estado_id">
		                        	<option value="">Seleccione</option>
		                        	<option ng-repeat="estado in estadosList track by $index" value="{{estado.id && '' || estado.id}}">{{estado.nombre && '' || estado.nombre}}</option>
		                        </select>
		                    </div>
		                </div>
		                <div class="row">
		                &nbsp;
		                </div>
		                <?php endif; ?>

		                <div class="row">
		                    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
		                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('cajas/listar'); ?>" class="btn btn-default btn-block">Cancelar</a></div>
		                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
		                        <button type="button" id="guardarBtn" class="btn btn-primary btn-block {{disabledBtn}}" ng-bind-html="guardarBtn" ng-click="guardar($event)" ng-disabled="configurado.length == 0">Guardar</button>
		                    </div>
		                </div>
		            </div>
		        </div>

			    <?php echo form_close(); ?>


	            <!-- Subpaneles -->
	            <?php if(!empty($caja_uuid)): ?>
				<div class="row" id="sub-panel">
					<div style="height:50px !important" class="panel-heading white-bg">
			    		<ul role="tablist" class="nav nav-tabs nav-tabs-xs">
							<li class="dropdown active"><a role="tab" data-toggle="tab" href="#tablaCobros">Cobros</a></li>
							<li class="dropdown"><a role="tab" data-toggle="tab" href="#tablaPagos">Pagos</a></li>
							<li class="dropdown"><a role="tab" data-toggle="tab" href="#tablaTransferencias">Transferencias</a></li>
							<li class="dropdown"><a role="tab" data-toggle="tab" href="#tablaDocumentos">Documentos</a></li>

						</ul>
					</div>

					<div class="tab-content white-bg p-xs">
						<div id="tablaCobros" class="tab-pane active" role="tabpanel"><?php echo modules::run('cobros/ocultotabla', $caja_id); ?></div>
						<div id="tablaPagos" class="tab-pane" role="tabpanel"><?php echo modules::run('pagos/ocultotabla', $caja_id); ?></div>
						<div id="tablaTransferencias" class="tab-pane" role="tabpanel"><?php echo modules::run('cajas/tablatransferencias', $caja_id); ?></div>
						<div id="tablaDocumentos" class="tab-pane" role="tabpanel"><?php echo modules::run('documentos/ocultotabla', $caja_id); ?></div>
					</div>
				</div>
					<br/><br/>
					<?php echo modules::run('cajas/ocultoformulariocomentarios'); ?>
	            <?php endif; ?>

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
<?php
echo Modal::config(array(
	"id" => "optionsModal",
	"size" => "sm"
))->html();
?>
optionsModal

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
                <div id="mensaje_info"></div>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>

				<div role="tabpanel">
				 	<!-- Tab panes -->
				  	<div class="row tab-content">
				    	<div role="tabpanel" class="tab-pane active" id="tabla">

							<!-- BUSCADOR -->
							<div class="ibox border-bottom">
								<div class="ibox-title">
									<h5>Buscar</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cuenta</label>
							            	<input type="text" id="nombre" class="form-control" value="" placeholder="">
										</div>

									</div>
									<div class="row">
							        	<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
										</div>
									</div>
								<!-- Termina campos de Busqueda -->
								</div>
							</div>
							<!-- /BUSCADOR -->

                <ul class="nav nav-tabs" id="cuentas_tabs_tabla">
                  <li class="active"><a href="javascript:void(0)" id="todos" class="filtro" data-item="0">Todas</a></li>
                  <li><a href="javascript:void(0)"  id="activo" data-item="1" class="filtro">Activos</a></li>
                  <li><a href="javascript:void(0)" id="pasivo" data-item="2" class="filtro">Pasivos</a></li>
                 <li><a href="javascript:void(0)" id="patrimonio" data-item="3" class="filtro">Patrimonio</a></li>
                 <li><a href="javascript:void(0)" id="ingreso" data-item="4" class="filtro">Ingresos</a></li>
								 <li><a href="javascript:void(0)" id="costo" data-item="5" class="filtro">Costos</a></li>
                 <li><a href="javascript:void(0)" id="gasto" data-item="6" class="filtro">Gastos</a></li>
              </ul>
				    		<!-- JQGRID -->
				    		<?php echo modules::run('contabilidad/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$opciones="<option value=''>Seleccione</option>";
foreach($impuestos as $impuesto){
  $opciones .= '<option value="'.$impuesto['id'].'">'.$impuesto['value'].'</option>';
}
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "crearPlanModal",
	"size" => "sm"
))->html();
$formAttr = array(
  'method'       => 'POST',
  'id'           => 'form_crear_cuenta',
  'autocomplete' => 'off'
);
echo Modal::config(array(
	"id" => "addCuentaModal",
  "contenido" => '<div class="row">
  <ul class="nav nav-tabs" id="cuentas_tabs">
<li class="active"><a href="javascript:" class="filter" data-item="0">Todas</a></li>
<li><a href="javascript:" data-item="1" class="filter">Activos</a></li>
<li><a href="javascript:" data-item="2" class="filter">Pasivos</a></li>
<li><a href="javascript:" data-item="3" class="filter">Patrimonio</a></li>
<li><a href="javascript:" data-item="4" class="filter">Ingresos</a></li>
<li><a href="javascript:" data-item="5" class="filter">Costos</a></li>
<li><a href="javascript:" data-item="6" class="filter">Gastos</a></li>
</ul>
                    <div class="col-md-4">
                        <h4>Selecione</h4>
                        <div id="plan_cuentas"></div>
                    </div>
                    <div class="col-md-5">'.form_open("", $formAttr).'

                        <div class="form-group"><label>Nombre</label>
                         <input id="nombre" name="nombre" type="text" placeholder="nombre" class="form-control" data-rule-required="true"/>
                         </div>

                        <div class="form-group">
                          <label>Codigo</label>
                          <input id="codigo" name="codigo" type="text" placeholder="codigo" class="form-control" data-rule-required="true" readonly>
                        </div>


                        <div class="form-group"><label>Descripci&oacute;n (Opcional) </label>
                        <input id="descripcion" name="descripcion" type="text"  class="form-control"/></div>
                        <input type="hidden" name="padre_id" id="padre_id"/>
                      '.form_close().'
                    </div>
                    <div class="col-md-3">
                      Cómo las cuentas afectan los reportes
                    </div>
                  </div>',
	"size" => "lg",
  "footer" =>'<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <input type="button" id="cancelarBtn" class="btn btn-default btn-block" value="Cancelar" />
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <input type="button" id="guardarBtn" class="btn btn-primary btn-block" value="Guardar" />
    </div>
  </div>'
))->html();
?>

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
	            <div class="row" ng-controller="toastController">
                  <?php //$mensaje = self::$ci->session->flashdata('mensaje'); ?>
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
				<?php
                                    $formAttr = array(
                                        'method'        => 'POST',
                                        'id'            => 'buscarFacturasComprasForm',
                                        'autocomplete'  => 'off'
                                    );

                echo form_open_multipart("", $formAttr);
                                ?>
				<div class="ibox border-bottom">
                                    <div class="ibox-title">
					<h5>Buscar facturas de compras</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
					</div>
                                    </div>

                                    <div class="ibox-content" style="display:none;">
					<!-- Inicia campos de Busqueda -->
                                        <div class="row">
                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="fecha1">No. factura</label>
                                                    <input type="text" class="form-control" id="numero_factura" />
                                            </div>
                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="fecha1">Rango de fechas</label>
                                                <div class="form-inline">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                            <input type="text" name="desde" id="fecha1" class="form-control">
                                                            <span class="input-group-addon">a</span>
                                                            <input type="text" class="form-control" name="hasta" id="fecha2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="proveedor">Proveedor</label>
                                                <select name="proveedor" class="form-control" id="proveedor3">
                                                    <option value="">Seleccione</option>
                                                    <?php foreach($proveedores as $proveedor) {?>
                                                    <option value="<?php echo $proveedor->id?>"><?php echo $proveedor->nombre?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="categoria">Categoría(s) de item</label><br>
                                                <select id="categoria_id" multiple class="form-control" data-placeholder=" ">
                                                    <option value=""> </option>
                                                    <?php foreach($categorias as $categoria):?>
                                                    <option value="<?php echo $categoria['id']?>"><?php echo $categoria['nombre']?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>


                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="monto1">Rango de montos</label>
                                                <div class="form-inline">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                                            <input type="text" name="monto1" id="monto1" class="form-control">
                                                            <span class="input-group-addon">a</span>
                                                            <input type="text" class="form-control" name="monto2" id="monto2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="centro_contable">Centro contable</label>
                                                <select name="centro_contable" class="form-control chosen-select" id="centro_contable">
                                                    <option value="">Seleccione</option>
                                                    <?php foreach($centros as $centro) {?>
                                                    <option value="<?php echo $centro['centro_contable_id']?>"><?php echo $centro['nombre']?></option>
                                                    <?php }?>
                                                </select>
                                            </div>

                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                  <label for="creado_por">Creado por</label>
                                                  <select name="creado_por" class="form-control chosen-select" id="creado_por">
                                                      <option value="">Seleccione</option>
                                                      <?php foreach($vendedores as $vendedor) {?>
                                                      <option value="<?php echo $vendedor['id']?>"><?php echo $vendedor['nombre']?></option>
                                                      <?php }?>
                                                  </select>
                                              </div>
                                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="estado">Estado</label>
                                                <select name="estado" multiple="true" class="form-control chosen-select" id="estado" data-placeholder="Seleccione">
                                                    <?php foreach($estados as $estado) {?>
                                                    <option value="<?php echo $estado->id?>"><?php echo $estado->valor?></option>
                                                    <?php }?>
                                                </select>
                                            </div>

                                          <!--  <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                <label for="tipo">Tipo</label>
                                                <select name="tipo" class="form-control chosen-select" id="tipo">
                                                    <option value="">Seleccione</option>
                                                    <?php foreach($tipos as $tipo) {?>
                                                    <option value="<?php echo $tipo->id?>"><?php echo $tipo->valor?></option>
                                                    <?php }?>
                                                </select>
                                            </div>-->
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
				<?php echo form_close(); ?>
				<!-- /BUSCADOR -->

				<!-- JQGRID -->
				<?php echo modules::run('facturas_compras/ocultotabla'); ?>

				<!-- /JQGRID -->
                <!-- REFACTURA-->
                <?php
                $formAttr = array('method' => 'POST','id' => 'refacturaForm','autocomplete'  => 'off');
                 echo form_open_multipart(base_url('facturas/refacturar'), $formAttr);?>
                    <input name="factura_compras[]" id="items_facturados" type="hidden"/>
                 <?php echo form_close(); ?>
                <!-- /REFACTURA-->
                            </div>

			</div>
                    </div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php

echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();

echo Modal::config(array( "id" => "optionsModal", "size"  => "sm"))->html();
?> <!-- modal opciones -->

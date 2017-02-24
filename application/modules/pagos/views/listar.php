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
                                    'method'       => 'POST',
                                    'id'           => 'buscarPagosForm',
                                    'autocomplete' => 'off'
                                );

                                echo form_open_multipart("", $formAttr);
                            ?>
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Pago</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
                                            <label for="">Rango de fechas</label>
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
                                            <label for="">Montos</label>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                      <span class="input-group-addon">De</span>
                                                      <input type="text" name="monto_min" id="monto_min" class="form-control">
                                                      <span class="input-group-addon">a</span>
                                                      <input type="text" class="form-control" name="monto_max" id="monto_max">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Forma de pago</label>
                                            <select name="forma_pago" class="form-control chosen-select" id="forma_pago">
                                                <option value="">Seleccione</option>
                                                <?php foreach($formas_pago as $forma_pago) {?>
                                                <option value="<?php echo $forma_pago->etiqueta?>"><?php echo $forma_pago->valor?></option>
                                                <?php }?>
                                            </select>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Tipo</label>
                                            <select name="tipo" class="form-control chosen-select" id="tipo">
                                                <option value="">Seleccione</option>
                                                <option value="compras">Compras</option>
                                                <option value="planilla">Planilla</option>
                                                <option value="pago_extraordinario">Pago extraordinario</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. documento</label>
                                               <input type="text" id="numero_documento" class="form-control" value="" placeholder="">
                                        </div>
                                         <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select name="estado" class="form-control chosen-select" id="estado">
                                                <option value="">Seleccione</option>
                                                <?php foreach($etapas as $etapa) {?>
                                                <option value="<?php echo $etapa->etiqueta?>"><?php echo $etapa->valor?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                        <!-- <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Banco</label>
                                            <select name="banco" class="form-control chosen-select" id="banco">
                                                <option value="">Seleccione</option>
                                                <?php foreach($bancos as $banco) {?>
                                                <option value="<?php echo $banco->id?>"><?php echo $banco->nombre?></option>
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
                            <?php echo modules::run('pagos/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php echo Modal::config(array( "id" => "optionsModal", "size"  => "sm"))->html();?> <!-- modal opciones -->
<?php //echo Modal::modalSubirDocumentos();?>  <!-- modal subir documentos -->
<?php

$formAttr = array('method' => 'POST', 'id' => 'aplicarPagosForm','autocomplete'  => 'off');
echo form_open(base_url('pagos/aplicar_pagos'), $formAttr);
echo form_close();

echo Modal::config(array(
	"id" => "aplicarPagosModal",
	"size" => "md"
))->html();?>

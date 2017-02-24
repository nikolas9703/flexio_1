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

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox float-e-margins border-bottom" id="buscador">
                                <!-- Filter Section Start -->
                                <div class="ibox-title">
                                    <h5><i class="fa fa-search"></i>&nbsp;Buscar documentos <small>Búsqueda avanzada</small></h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox">
                                    <div class="ibox-content" style="display:none;">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Relacionado a</label>
                                                <input type="text" class="form-control" id="relacionado_a">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Tipo de documento</label>
                                                <select class="select2" multiple="" id="tipo_id">
                                                    <option value="">Seleccione</option>
                                                    <?php foreach ($tipos_documento as $key => $value):?>
                                                    <option value="<?php echo $value['id']?>"><?php echo $value['nombre']?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="">Rango de Fecha de carga</label>
                                                <div class="form-inline">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                          <input type="text" class="form-control" id="fecha_desde">
                                                          <span class="input-group-addon">a</span>
                                                          <input type="text" class="form-control" id="fecha_hasta">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Centro contable</label>
                                                <select class="select2" multiple="" id="centro_contable_id">
                                                    <option value="">Seleccione</option>
                                                    <?php foreach ($centros_contables as $key => $value):?>
                                                    <option value="<?php echo $value['id']?>"><?php echo $value['nombre']?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Usuario</label>
                                            <select class="select2" multiple="" id="subido_por">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($usuarios as $key => $value):?>
                                                <option value="<?php echo $value['id']?>"><?php echo $value['nombre']." ".$value['apellido']?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Estado</label>
                                            <select class="select2" multiple="" id="etapa">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($estados as $key => $value):?>
                                                <option value="<?php echo $value['etiqueta']?>"><?php echo $value['valor']?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Filter Button Section Start -->
                                    <div class="row">
                                        <div class="hr-line-dashed"></div>
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <a class="btn btn-default btn-block btn-facebook" href="#" id="clearBtn">
                                                <i class="fa fa-eraser"> </i> Limpiar
                                            </a>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <a class="btn btn-default btn-block btn-facebook" href="#" id="searchBtn">
                                                <i class="fa fa-search"> </i> Filtrar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Filter Button Section End -->
                        </div>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <?php echo modules::run('documentos/ocultotabla_main'); ?>

                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
                <a href="../config/routes.php"></a>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php echo Modal::config(array( "id" => "optionsModal", "size"  => "sm"))->html();?> <!-- modal opciones -->
<?php echo Modal::config(array( "id" => "actualizarModal", "size"  => "lg"))->html();?> <!-- modal opciones -->
<?php //echo Modal::modalSubirDocumentos();?>  <!-- modal subir documentos -->
<?php

$formAttr = array('method' => 'POST', 'id' => 'aplicarPagosForm','autocomplete'  => 'off');
echo form_open(base_url('pagos/aplicar_pagos'), $formAttr);
echo form_close();

echo Modal::config(array(
	"id" => "aplicarPagosModal",
	"size" => "md"
))->html();?>


<?php
$formAttr = array('method' => 'POST', 'id' => 'descargarZipDocumentos','autocomplete'  => 'off');
echo form_open(base_url('documentos/ajax_descargar_zip'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>

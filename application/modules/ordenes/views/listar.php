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
                <!-- <div class="row">
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php //echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>-->
                <div class="row" ng-controller="toastController">
                    <?php //$mensaje = self::$ci->session->flashdata('mensaje'); ?>
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                    </div>
                </div>
                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar &oacute;rdenes de compra</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="No_oc">No. O/C</label><br>

                                                 <input type="text" name="numero" id="numero" class="form-control">



                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
					<label for="">Rango de fechas</label>
                                         <div class="form-inline">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                            <input type="text" name="fecha_desde" id="fecha_desde" class="form-control">
                                                            <span class="input-group-addon">a</span>
                                                            <input type="text" class="form-control" name="fecha_hasta" id="fecha_hasta">
                                                        </div>
                                                    </div>
                                                </div>

					<!--<div class="input-group">
					<input type="input" id="fecha_desde" readonly="readonly" class="form-control" value="" />
					<span class="input-group-addon">a</span>
					<input type="input" id="fecha_hasta" readonly="readonly" class="form-control" value="" />
					</div>-->
					</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="proveedor">Proveedor</label><br>
                                            <select id="proveedor3" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($proveedores as $proveedor):?>
                                                <option value="<?php echo $proveedor->uuid_proveedor?>"><?php echo $proveedor->nombre?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="montos_de">Rango de montos</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                                <input type="text" class="form-control" id="montos_de" placeholder="" data-inputmask="'mask':'9{0,8}.{0,1}9{0,2}','greedy':false">
                                                <div class="input-group-addon">a </div>
                                                <input type="text" class="form-control" id="montos_a" placeholder="" data-inputmask="'mask':'9{0,8}.{0,1}9{0,2}','greedy':false">
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="centro">Centro contable</label><br>
                                            <select id="centro" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($centros as $centro):?>
                                                <option value="<?php echo $centro['id']?>"><?php echo $centro['nombre']?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                         <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="creado_por">Creado por</label><br>
                                            <select id="creado_por" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($usuarios as $usuario):?>
                                                <option value="<?php echo $usuario->id?>"><?php echo $usuario->nombre.' '.$usuario->apellido?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                       <!-- <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="referencia">Referencia</label>
                                            <input type="text" id="referencia" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="numero">N&uacute;mero</label>
                                            <input type="text" id="numero" class="form-control" value="" placeholder="">
                                        </div>-->

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
                                            <label for="estado">Estado</label><br>
                                            <select id="estado" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($estados as $estado):?>
                                                <option value="<?php echo $estado->id_cat?>"><?php echo $estado->etiqueta?></option>
                                                <?php endforeach;?>
                                            </select>
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

                            <!-- JQGRID -->
                            <?php echo modules::run('ordenes/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>

                        <div role="tabpanel" class="tab-pane" id="grid">
                            <?php
                                //QUITO ESTOS ELEMENTOS DEL ARRAY
                                //PARA EVITAR CONFLICTOS DE INDICES INDEFINIDOS
                                unset($vars["estados"]);
                                unset($vars["centros"]);
                                unset($vars["proveedores"]);
                            ?>
                            <?php //Grid::visualizar_grid($vars); ?>
                        </div>

                    </div>
                </div>
            </div>

    	</div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarOrdenes','autocomplete'  => 'off');
echo form_open(base_url('ordenes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php

echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();

    echo    Modal::config(array(
                "id"    => "optionsModal",
                "size"  => "sm"
            ))->html();

?>

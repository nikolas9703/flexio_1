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
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
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
                                    <h5>Buscar salidas de inventario</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="fecha_desde">Rango de fechas</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i> </div>
                                                <input type="text" class="form-control" id="fecha_desde" placeholder="">
                                                <div class="input-group-addon">a </div>
                                                <input type="text" class="form-control" id="fecha_hasta" placeholder="">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="destino">Destino</label><br>
                                            <select id="destino" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($destinos as $destino):?>
                                                <option value="<?php echo $destino["uuid"]?>"><?php echo $destino["nombre"]?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="enviar_desde">Enviar desde</label><br>
                                            <select id="enviar_desde" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($bodegas as $bodega):?>
                                                <option value="<?php echo $bodega->uuid_bodega?>"><?php echo $bodega->nombre?></option>
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
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" style="clear: both;">
                                            <label for="numero">N&uacute;mero</label>
                                            <input type="text" id="numero" class="form-control" value="" placeholder="">
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="tipo">Tipo</label><br>
                                            <select id="tipo" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <option value="Flexio\Modulo\Ajustes\Models\Ajustes">Ajuste</option>
                                                <option value="Flexio\Modulo\Consumos\Models\Consumos">Consumo</option>
                                                <option value="Flexio\Modulo\Traslados\Models\Traslados">Traslado</option>
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
                            <?php echo modules::run('salidas/ocultotabla'); ?>
				    		
                            <!-- /JQGRID -->
                        </div>
                        
                        <div role="tabpanel" class="tab-pane" id="grid">
                            <?php
                                //QUITO ESTOS ELEMENTOS DEL ARRAY
                                //PARA EVITAR CONFLICTOS DE INDICES INDEFINIDOS
                                //unset($vars["estados"]);
                                //unset($vars["centros"]);
                                //unset($vars["proveedores"]);
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

    echo    Modal::config(array(
                "id"    => "optionsModal",
                "size"  => "sm"
            ))->html();

?>

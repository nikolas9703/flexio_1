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
                            <?php
                            $formAttr = array(
	                            'method'       => 'POST',
	                            'id'           => 'exportarEntradas',
	                            'autocomplete' => 'off'
                            );

                            echo form_open_multipart(base_url('entrada_manual/exportar'), $formAttr);
                            ?>
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><i class="fa fa-search"></i>&nbsp;Buscar entrada manual <small>Búsqueda avanzada</small></h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-down"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox">
                                <div class="ibox-content" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Centro contable</label>
                                            <select data-placeholder="Seleccione" class="chosen-select" multiple="multiple" id="centro_contable" tabindex="-1" name="centro_contable[]">
                                                <?php foreach ($centros_contable as $key => $centro) { ?>
                                                    <option value="<?php echo $centro->id ?>"><?php echo $centro->nombre ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Rango de Fecha</label>
                                            <div class="input-daterange input-group" id="fecha">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" class="input-sm form-control" id="fecha_min" name="fecha_min">
                                                <span class="input-group-addon">a</span>
                                                <input type="text" class="input-sm form-control" id="fecha_max" name="fecha_max">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Filter Button Section Start -->
                                    <div class="row">
                                        <div class="hr-line-dashed"></div>
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <a class="btn btn-default btn-block" id="clearBtn">
                                                    <i class="fa fa-eraser"> </i> Limpiar</a>

                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <a class="btn btn-default btn-block" id="searchBtn">
                                                    <i class="fa fa-search"> </i> Filtrar</a>

                                        </div>
                                    </div>
                                    <!-- Filter Button Section End -->
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>

				    		<!-- JQGRID -->
				    		<?php echo modules::run('entrada_manual/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
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

?>

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
	                  <div class="alert alert-dismissable <?php echo !empty($data['mensaje']) ? 'show '.$data['mensaje']["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                    <?php echo !empty($data['mensaje']) ? $data['mensaje']["contenido"] : ''  ?>
	                </div>
	            </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">


                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Contacto</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-sm-3">
                                            <label for="">Nombre del Contacto</label>
                                            <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="">Cliente</label>
                                            <input type="text" id="cliente" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="">Tel&eacute;fono</label>
                                            <input type="text" id="telefono" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="">E-mail</label>
                                            <input type="text" id="email" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-2 pull-right">
                                            <input type="button" id="clearBtn" class="btn btn-w-m btn-default btn-block" value="Limpiar" />
                                        </div>
                                        <div class="form-group col-sm-2 pull-right">
                                            <input type="button" id="searchBtnCon" class="btn btn-w-m btn-default btn-block" value="Filtrar" />
                                        </div>
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <!-- /BUSCADOR -->

				    		<?php echo modules::run('contactos/ocultotabla'); ?>
				    	</div>
                        <div role="tabpanel" class="tab-pane" id="grid">

				            <?php //Grid::visualizar_grid($vars['grid']); ?>
				    	</div>
                    </div>
                </div>

        </div>
    </div>
</div>

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
                <div class="alert alert-success alert-dismissable <?php echo !empty($message) ? 'show' : 'hide'  ?>">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo !empty($message) ? $message : ''  ?>
                </div>

                <!-- BUSCADOR -->
                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5>Buscar M&oacute;dulos</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content" style="display:none;">
                        <!-- Inicia campos de Busqueda -->
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-4 col-md-4">
                                <label for="">Nombre</label>
                                <input type="text" id="nombre" class="form-control" value="" placeholder="">
                            </div>
                            <div class="form-group col-xs-12 col-sm-4 col-md-4">
                                <label for="">Estado</label>
                                <select id="estado" class="form-control">
                                	<option value="">Seleccione</option>
                                	<option value="1">Activo</option>
                                	<option value="0">Inactivo</option>
                            	</select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4 col-md-4">
                                <label for="">Descripci&oacute;n</label>
                                <input type="text" id="descripcion" class="form-control" value="" placeholder="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-0 col-sm-9 col-md-9">&nbsp;</div>
                          
                            <div class="form-group col-xs-12 col-sm-3 col-md-3" style="text-align: right;">
                            	<input type="button" id="searchBtn" class="btn btn-w-m btn-default" value="Filtrar" />
                                <input type="button" id="clearBtn" class="btn btn-w-m btn-default" value="Limpiar" />
                            </div>
                            
                        </div>
                        <!-- Termina campos de Busqueda -->
                    </div>
                </div>
                <!-- /BUSCADOR -->
            </div>
            
            <div id="jqgrid-column-togle" class="row"></div>
            
             <!-- Listado de Modulos -->
            <div class="row">
            	<div class="NoRecords text-center lead"></div> 
                        
            	
            	
            	<!-- the grid table -->
            	<table class="table table-striped " id="modulosGrid"></table>
        
             	<!-- pager definition -->
             	<div id="pager"></div>
            </div>
            <!-- /Listado de Modulos -->
            
            
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" id="optionsModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Opciones</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

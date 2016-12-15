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
									<h5>Buscar Registro de Tiempo</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->
									
									<?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'buscarColaboradorForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
			                        
			                        
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Colaborador</label>
							            	<select id="colaborador_id" name="colaborador_id" class="form-control" >
								                <option value="">Seleccione</option>
								                <?php
								                if(!empty($colaboradores))
								                { 
									                foreach ($colaboradores AS $colaborador)
									                {
									               		echo '<option value="'. $colaborador->id .'">'. $colaborador->nombre .'</option>';
									                }
								                }
								                ?>
							                </select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">C&eacute;dula</label>
							            	<select id="cedula" name="cedula" class="form-control" >
								                <option value="">Seleccione</option>
								                <?php
								                if(!empty($colaboradores))
								                { 
									                foreach ($colaboradores AS $colaborador)
									                {
									               		echo '<option value="'. $colaborador->id .'">'. $colaborador->cedula .'</option>';
									                }
								                }
								                ?>
							                </select>
										</div>
										<div>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	 
										</div>
									</div>
 
									<div class="row">
							        	<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
											
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="searchBtn" class="btn btn-primary btn-block" value="Filtrar" />
										</div>
									</div>
									 <?php echo form_close(); ?>
								<!-- Termina campos de Busqueda -->
								</div>
							</div>
							<!-- /BUSCADOR -->
				    	
				    		<!-- JQGRID -->
 				    		<?php  
   				    		echo modules::run('planilla/ocultotabla_hojas', isset( $info) ?$info:array());?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<!-- inicia #optionsModal -->
<div class="modal fade bs-example-modal-sm" id="opcionesModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
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
<!-- termina #optionsModal -->

<!-- inicia #optionsModal -->
<div class="modal fade bs-example-modal-sm" id="actualizarModal" tabindex="-1" role="dialog" aria-labelledby="actualizarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Actualizar</h4>
            </div>
            <div class="modal-body">
            	<label for="">Etapa de Venta</label>
				<select id="id_etapa" class="form-control">
	                <option value="">Seleccione</option>
	                <?php
	                if(!empty($etapas_venta))
	                {
		                foreach ($etapas_venta AS $etapa)
		                {
		               		echo '<option value="'. $etapa['id_cat'] .'">'. $etapa['etiqueta'] .'</option>';
		                }
	                }
	                ?>
                </select>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- termina #optionsModal -->

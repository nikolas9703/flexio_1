<div id="wrapper">
   
    <?php 
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">
    
	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

	    <div class="wrapper wrapper-content">
	    <!-- CONTENT WRAPPER -->
	    
	    	<div class="panel-group" aria-multiselectable="true" role="tablist">
	    		
	    		<div class="panel panel-blanco" id="accordeonCatalogos">
					<div class="panel-heading panel-blanco-heading">
						<h5 class="panel-title">
							<a class="" data-toggle="collapse" data-parent="#administrador_de_actividades" href="#collapse-administrador_de_actividades" aria-expanded="true">Oportunidades</a>
						</h5>
					</div>
					<div style="" id="collapse-administrador_de_actividades" class="panel-collapse collapse in" aria-expanded="true">
						<div class="panel-body">
						
							<div aria-multiselectable="true" role="tablist" class="panel-group">
								<div class="panel panel-default">
									<div id="heading-actividades" role="tab" class="panel-heading">
										<h4 class="panel-title">
											<a aria-controls="collapse-actividades" aria-expanded="false" href="#collapse-actividades" data-parent="#accordion-addons" data-toggle="collapse" class="accordion-toggle collapsed">
											Catalogo: Etapa de Venta</a>
										</h4>
									</div>
									<div aria-labelledby="heading-actividades" role="tabpanel" class="panel-collapse collapse" id="collapse-actividades" aria-expanded="false" style="height: 0px;">
										<div class="panel-body">

										 <?php Catalogos::formulario("oportunidadEtapaVenta", "oportunidades", "id_etapa_venta"); ?>										
			
										</div>
									</div>
								</div>
							</div>
				
						</div>
					</div>
				</div>
	    		
	    	</div>
		
		<!-- /END CONTENT WRAPPER -->
		</div>

	</div>
</div>

<?php echo Modal::modalOpciones(); ?>

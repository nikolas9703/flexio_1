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
            <div role="tabpanel" class="tab-pane active" id="tabs-configuracion">

              <?php
              $formAttr = array(
                  'method' => 'POST',
                  'id' => 'configuracion_subcontrato',
                  'autocomplete' => 'off',
                  'class' => 'vue-config-subcontrato animated fadeIn'
              );
              echo form_open(base_url('configuracion_contratos/configuracion'), $formAttr);
              ?>

              <!-- TABS HERE -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tipo-subcontrato">Tipo de subcontrato</a></li>
                </ul>
                <div class="tab-content white-bg">
                    <div id="tipo-subcontrato" class="tab-pane active">
                        <div class="panel-body">

                            <!-- componente tipo subcontrato -->
                            <tipo_subcontrato></tipo_subcontrato>
                            <!-- /componente tipo subcontrato -->

                            <div class="row">&nbsp;</div>

                            <!-- JQGRID -->
            				    		<?php echo modules::run('configuracion_contratos/ocultotablaCatalogoTipoSubcontratos'); ?>

                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
              <!-- /TABS HERE -->

            </div>
          </div>
          <!-- /Tab panes -->
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php
echo Modal::config(array( "id" => "opcionesModal", "size"  => "sm"))->html();?> <!-- modal opciones -->
<?php //echo Modal::modalSubirDocumentos();?>  <!-- modal subir documentos -->

<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="reportes_financieros">
	            <div class="row">
                <div id="mensaje_info"></div>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["mensaje"] : ''  ?>
	                </div>
	            </div>
              <?php
              $formAttr = array(
                'method'       => 'POST',
                'id'           => 'form_reporte_exportar',
                'autocomplete' => 'off'
              );

            echo form_open(base_url('reportes_financieros/exportar'), $formAttr);?>


              <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 header-ventas">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="text-header">Empezar el reporte financiero desde </div>
                  </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select  class="form-control" name="campo[tipo]" id="tipo" v-model="cabecera.reporte_actual" @change="seleccionarAplicar(cabecera.reporte_actual)" :disabled="disabledCabecera">
                  <?php foreach($catalogo as $reporte){ ?>
                    <option value="<?php echo $reporte->etiqueta ?>"><?php echo $reporte->valor ?></option>
                  <?php }?>
                </select>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"></div>
              </div>

              <div class="ibox border-bottom">
                  <div class="ibox-title">
                      <h5>Par&aacute;metros: <span v-html="tituloReporte"></span></h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>
                  <div class="ibox-content" style="display:block;">
      	             <div class="row">
                       <!--component formulario-->

                          <component :is="reporteActual"></component>

                       <!--component formulario-->
                     </div>
                 </div>
            </div>
            <!-- compoenente reporte -->

            <!--<reporte :info.sync="dataReporte" v-if="dataReporte.length > 0"></reporte> -->
            <component :is="reporte" v-if="dataReporte.length > 0" :info.sync="dataReporte" ></component>
            <!-- compoenente reporte -->
              <?php  echo  form_close();?>
        	</div> <!-- fin vue padre -->
          <!-- para la carga de template -->
          <?php   echo modules::run('reportes_financieros/ocultoformulario');?>
          <!-- para la carga de template -->
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

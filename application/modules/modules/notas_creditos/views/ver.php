<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="crear_nota_credito">
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
                'id'           => 'form_crear_notaCredito',
                'autocomplete' => 'off'
              );

            echo form_open(base_url('notas_creditos/guardar'), $formAttr);?>


              <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 header-ventas">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="text-header">Aplicar nota de cr&eacute;dito a </div>
                  </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select  class="form-control" name="campo[tipo]" id="tipo" v-model="cabecera.tipo" @change="seleccionarAplicar()" :disabled="disabledCabecera">
                  <option value="">Seleccione</option>
                  <option value="factura">Factura</option>
                </select>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select  class="form-control" name="tipoId" id="tipoId" v-model="cabecera.tipoId" @change="llenarFormulario(cabecera.tipoId)" :disabled="disabledCabecera">
                  <option value="">Seleccion</option>
                  <option v-for="option in cabecera.coleccion" v-bind:value="option.id">
                          {{ option.cliente.nombre + ' - ' + option.codigo}}
                  </option>
                </select>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"></div>
              </div>


              <div class="ibox border-bottom">
                  <div class="ibox-title">
                      <h5>Datos de la nota de crédito</h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>

                  <div class="ibox-content" style="display:block;">
      	            <div class="row">
                      	<?php
                      		$info = !empty($info) ? array("info" => $info) : array();
                      		 echo modules::run('notas_creditos/ocultoformulario', $info);
                      	?>
                    </div>
            </div>
          </div>
              <?php  echo  form_close();?>


              <div class="row">
                  <component :is="vista_comments" :historial.sync="comentarios" ></componente>            
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

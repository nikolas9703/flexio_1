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
                <?php $mensaje = self::$ci->session->flashdata('mensaje'); ?>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>
              <?php
              $formAttr = array(
                'method'       => 'POST',
                'id'           => 'form_crear_presupuesto',
                'autocomplete' => 'off'
              );
            echo form_open(base_url('presupuesto/guardar'), $formAttr);?>

              <div class="ibox border-bottom">
                  <div class="ibox-title">
                      <h5>Datos del Presupuesto</h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>

                  <div class="ibox-content" style="display:block;">
      	            <div class="row">
                      	<?php
                      		$info = !empty($info) ? array("info" => $info) : array();
                      		 echo modules::run('presupuesto/ocultoformulario', $info);

                      	?>
                    </div>
            </div>
          </div>
              <div class="row">
                <?php
                  echo modules::run('presupuesto/ocultocomponente_presupuesto');
               ?>
               <component :is="componenteTipo" :datos-tabla.sync="datosTabla" :show-guardar="showActualizar"></component>
              </div>
          <?php  echo  form_close();?>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "presupuestoFillTableModal",
	"size" => "md",
  "contenido" => '<div class="row">
                 <div class="col-md-12">

                     <div class="form-group">
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                       <input type="radio" name="ajuste" id="ajuste1" value="monto_fijo"/>
                       <label>Aplicar monto fijo a cada mes:</label>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                       <input id="monto_fijo" name="monto_fijo" type="text" class="form-control aplicar moneda" data-rule-required="true"/>
                       <input id="aux_monto" type="hidden"/>
                    </div>
                    </div>
                    <div class="form-group">
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                      <input type="radio" name="ajuste" id="ajuste2" value="monto_mensual"/>
                      <label>Ajustar por monto cada mes:</label>
                      </div>
                      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                      <input id="monto_mensual" name="monto_mensual" type="text" class="form-control aplicar moneda" data-rule-required="true"/>
                    </div>
                   </div>
                   <div class="form-group">
                   <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                     <input type="radio" name="ajuste" id="ajuste3" value="monto_porcentaje"/>
                     <label>Ajustar por porcentaje cada mes:</label>
                   </div>
                   <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                     <div class="input-group input-group-sm">
                    <span class="input-group-addon" id="sizing-addon3">%</span>
                    <input type="text" id="monto_porcentaje" name="monto_porcentaje" class="form-control aplicar porcentaje" aria-describedby="sizing-addon3"/>
                  </div>
                  </div>
                  </div>


                 </div>
            </div>',
  "footer" => '<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-0 col-sm-0 col-md-6 col-lg-6">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
      <input type="button" id="cancelarBtn" class="btn btn-default btn-block" value="Cancelar" />
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
      <input type="button" id="aplicarBtn" class="btn btn-primary btn-block" value="Aplicar" />
    </div>
  </div>',
))->html();
?>

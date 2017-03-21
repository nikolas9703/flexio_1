
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/assets/css/default/bootstrap.min.css') ?>" >
<link href="<?php echo base_url();?>public/assets/css/default/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/default/custom.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/default/font-awesome.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/default/animate.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/themes/erp/css/style.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/plugins/jquery/toastr.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/plugins/bootstrap/bootstrap-tabdrop.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/plugins/bootstrap/jasny-bootstrap.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/resources/compile/css/flexio.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/modules/stylesheets/login.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/default/ui/base/jquery-ui.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/default/ui/base/jquery-ui.theme.css" rel="stylesheet">




<script>
   // $("#tipo_documento").change(function(){



       // var tipo_identificacion = $('#tipo_identificacion option:selected').val();

         // var tipo_documento = $('#tipo_documento option:selected').val();
      // alert(tipo_documento);

         //if (tipo_documento == "OV") {
              // document.getElementById('etiqueta_numero_documento').innerHTML = 'C&eacute;dula/RUC';
           //      document.getElementById('etiqueta_numero_documento').innerHTML = 'No. &Oacute;rden de Venta';
         //}

      //   if (tipo_documento == "OC") {
        //         document.getElementById('etiqueta_numero_documento').innerHTML = 'No. &Oacute;rden de Compra';
    //     }


  //});

</script>





<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <p>&nbsp;</p>
        <div class="imagelogo">
            <img id="logo" src="<?php echo base_url();?>public/assets/images/logo_flexio_background_transparent_recortado.png" alt="BluLeaf" border="0">
        </div>
        <div class="alert  hide alert-dismissable">
            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">x</button>
        </div>


		<?= form_open("verified_by_flexio/valida_documento", array("autocomplete" => "off") )?>
        <div class="form-group col-lg-12">
            <label>No. de Documento</label>
            <input type="text" name="numero_documento" class="form-control" value="">
        </div>

        <div class="form-group col-lg-12">
            <label>Fecha Documento (DD/MM/YYYY)</label>
            <input type="text" name="fecha_documento" class="form-control" id="fecha" value="">
        </div>

      <!--  <div class="form-group col-lg-12">
            <label>Cantidad de Art&iacute;culos</label>
            <input type="text" name="cantidad_articulos" class="form-control" value="">
        </div>-->

        <div class="form-group col-lg-12">
            <label>Monto del Documento:</label>
            <input type="text" name="monto_documento" class="form-control" value="">
        </div>

        <button type="submit" class="btn btn-primary block full-width m-b">Verificar</button>
        </form>
        <p>Verified by Flexio</p>
        <p class="m-t"> <small>Desarrollado por Pensanomica © 2016</small> </p>
    </div>
</div>


<!-- Este div se usa para mostrar las alertas del sistema flexio -->
<div id="z_flexio_div">
	<toast_v2 :mensaje.sync="mensaje"></toast_v2>
</div>

<?php Assets::js_vars(); ?>
<script src="<?php echo base_url('public/resources/compile/js/flexio.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/vue.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/resources/compile/modulos/zflexio/zflexio.js') ?>" type="text/javascript"></script>
<script>

    $(document).ready(function(){
        $('#fecha').datepicker({
    		dateFormat: 'dd/mm/yy',
    		changeMonth: true,
    		numberOfMonths: 1
    	});
    });

</script>

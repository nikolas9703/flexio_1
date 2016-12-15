
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/assets/css/default/bootstrap.min.css') ?>" >




    
 
    
    
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



<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-6">
				<h3 class="dark-grey">Verified by Flexio</h3>
				<?= form_open("verified_by_flexio/valida_codigo_no_cambia_documento", array("autocomplete" => "off") )?>
				<div class="form-group col-lg-12">
					<label>No. de Documento</label>
					<input type="" name="numero_documento" class="form-control" id="" value="">
				</div>
				
				<div class="form-group col-lg-12">
					<label>Fecha Documento (AAAA/MM/DD)</label>
					<input type="" name="fecha_documento" class="form-control" id="" value="">
				</div>
				
				<div class="form-group col-lg-12">
                                    <label>Cantidad de Art&iacute;culos</label>
					<input type="" name="cantidad_articulos" class="form-control" id="" value="">
				</div>
								
				<div class="form-group col-lg-12">
					<label>Monto del Documento:</label>
					<input type="" name="monto_documento" class="form-control" id="" value="">
				</div>
				
                                <!--
				<div class="form-group col-lg-6">
					<label>Repeat Email Address</label>
					<input type="" name="" class="form-control" id="" value="">
				</div>			
				<div class="col-sm-6">
					<input type="checkbox" class="checkbox" />Sigh up for our newsletter
				</div>

				<div class="col-sm-6">
					<input type="checkbox" class="checkbox" />Send notifications to this email
				</div>				
			-->
				<button type="submit" class="btn btn-primary">Verificar</button>
                                <button type="cancel" class="btn btn-secondary">Cancelar</button> 
			</div>
		
			<div class="col-md-6">
                            <h3 class="dark-grey">Verificaci&oacute;n</h3>
                                <div class="alert-danger alert-dismissable <?php echo !empty($mensaje_valida_codigo_existe_documento_error)? 'show' : 'hide'  ?>" style="padding:10px;">                             
                                   <!-- <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>-->
                                    <p class="lead" style="padding:0px;margin:0px;"><i class="fa fa-fw fa-save"></i><?php echo $mensaje_valida_codigo_existe_documento_error; ?></p>
                                </div>
                            
                                <div class="alert-success alert-dismissable <?php echo !empty($mensaje_valida_codigo_existe_documento)? 'show' : 'hide'  ?>" style="padding:10px;">                             
                                    <!--<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> -->
                                    <p class="lead" style="padding:0px;margin:0px;"><i class="fa fa-fw fa-save"></i><?php echo $mensaje_valida_codigo_existe_documento; ?></p>
                                </div>
                            
                            
                            <?php 
                                $valida_codigo_no_cambia_documento = "";
                                $valida_codigo_no_cambia_documento_error = "";
                                
                                if ($this->session->userdata('mensaje_valida_codigo_no_cambia_documento')) {
                                $valida_codigo_no_cambia_documento = $this->session->userdata('mensaje_valida_codigo_no_cambia_documento');
                                } else {
                                    if ($this->session->userdata('mensaje_valida_codigo_no_cambia_documento_error')) {
                                        $valida_codigo_no_cambia_documento_error = $this->session->userdata('mensaje_valida_codigo_no_cambia_documento_error');
                                    }
                                }
                                
                                $this->session->unset_userdata('mensaje_valida_codigo_no_cambia_documento_error');
                                $this->session->unset_userdata('mensaje_valida_codigo_no_cambia_documento');
        
                            ?>
                                <div class="alert-danger alert-dismissable <?php echo !empty($valida_codigo_no_cambia_documento_error)? 'show' : 'hide'  ?>" style="padding:10px;">                             
                                    <!--<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> -->
                                    <p class="lead" style="padding:0px;margin:0px;"><i class="fa fa-fw fa-save"></i><?php echo $valida_codigo_no_cambia_documento_error; ?></p>
                                </div>
                            
                                <div class="alert-success alert-dismissable <?php echo !empty($valida_codigo_no_cambia_documento)? 'show' : 'hide'  ?>" style="padding:10px;">                             
                                   <!-- <button aria-hidden="true" data-dismiss="success" class="close" type="button">×</button> -->
                                    <p class="lead" style="padding:0px;margin:0px;"><i class="fa fa-fw fa-save"></i><?php echo $valida_codigo_no_cambia_documento; ?></p>
                                </div>
                            
                            
				<p>
					El documento ha sido generado por el sistema Flexio
				</p>
				<p>
                                        La informaci&oacute;n del documento ha sido validado por el sistema Flexio.
				</p>
				
				
			</div>
                     <?= form_close() ?>
                    
                     
		</div>
	</section>
</div>




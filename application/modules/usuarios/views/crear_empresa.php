<div id="wrapper">
    <?php //Template::cargar_vista('sidebar'); ?>
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

	            <div class="row">
                <div class="tab-content">
                  <div class="tab-pane active">
                <div id="crear-empresa" class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                <?php
                $formAttr = array(
                  "method"        => "post",
                  "id"            => "crearEmpresaForm",
                  "class" 		=> "form-horizontal ". (isset($message) && !empty($message) ? "animated shake" : ""),
                  "autocomplete"  => "off"
                );
                echo form_open_multipart(base_url(uri_string()), $formAttr);
                ?>
                     <div class="ibox">
                       <div class="ibox-title border-bottom">
      					         	<h5>Empresa</h5>
      					        	<div class="ibox-tools">
      							     <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
      						     </div>
					           </div>
                     <div class="ibox-content m-b-sm">

                        <div class="row col-lg-6 col-md-6">
                            <div class="form-group col-lg-12 col-md-12">
                                  <label>Logo de la Empresa</label>
                                <input type="file" name="logo" class="form-control" placeholder="logo" value="<?php echo set_value('logo'); ?>">
                                <?php echo form_error('logo', '<div class="error">', '</div>'); ?>
                            </div>

                            <div class="form-group col-lg-12 col-md-12">
                                  <label> Nombre de la Empresa <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[nombre]" class="form-control" placeholder="Nombre de la Empresa" value="<?php echo set_value('campo[nombre]'); ?>">
                                <?php echo form_error('campo[nombre]', '<div class="error">', '</div>'); ?>
                            </div>

                            <div class="form-group col-lg-12 col-md-12">
                              <label> Direcci&oacute;n <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[descripcion]" class="form-control" placeholder="Direcci&oacute;n" value="<?php echo set_value('campo[descripcion]'); ?>">
                                <?php echo form_error('campo[descripcion]', '<div class="error">', '</div>'); ?>
                            </div>
                        </div>
                        <div class="row col-lg-6 col-md-6">

                            <div class="form-group col-lg-12 col-md-12">
                              <label>Tel&eacute;fono <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[telefono]" class="form-control" placeholder="Tel&eacute;fono" >
                                <?php echo form_error('campo[telefono]', '<div class="error">', '</div>'); ?>
                            </div>
                            <div class="form-group col-lg-12 col-md-12">
                              <label>RUC <span required="" aria-required="true">*</span></label>
                              <div class="row">
                                <div class="col-lg-3 col-md-3" style="padding-left:0">
                                 <input type="text" name="campo[tomo]" class="form-control" placeholder="Tomo">
                                 <?php echo form_error('campo[tomo]', '<div class="error">', '</div>'); ?>
                               </div>
                               <div class="col-lg-3 col-md-3">
                                <input type="text" name="campo[folio]" class="form-control" placeholder="Folio">
                                <?php echo form_error('campo[folio]', '<div class="error">', '</div>'); ?>
                              </div>
                              <div class="col-lg-3 col-md-3">
                               <input type="text" name="campo[asiento]" class="form-control" placeholder="Asiento">
                               <?php echo form_error('campo[asiento]', '<div class="error">', '</div>'); ?>
                             </div>
                             <div class="col-lg-3 col-md-3">
                              <input type="text" name="campo[digito_verificador]" class="form-control" placeholder="Digito Verificador">
                              <?php echo form_error('campo[digito_verificador]', '<div class="error">', '</div>'); ?>
                            </div>
                              </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12">
                              <label>Retiene Impuestos: <span required="" aria-required="true">*</span></label>
                              
                                  <select name="campo[retiene_impuesto]" class="form-control">
                                  <option value="">Seleccione</option>
                                      <option value="si">S&iacute;</option>
                                      <option value="no">No</option>
                                  </select>
                                <?php echo form_error('campo[retiene_impuesto]', '<div class="error">', '</div>'); ?>
                            </div>
                        </div>



                        <div class="row">
                        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8"></div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                              <a href="<?php echo base_url().'usuarios/listar_empresa/'.self::$ci->session->userdata('uuid_organizacion');?>" class="btn btn-default btn-block">Cancelar</a>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 pull-right">
                                <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                            </div>
                        </div>
                      </div>
                    </div>
                    </div>
                    </div>
                    <?php echo form_close(); ?>
                  </div>
                  </div>
                  </div>
                </div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

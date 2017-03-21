<div id="wrapper">
    <?php Template::cargar_vista('sidebar'); ?>
    <div id="page-wrapper" class="gray-bg row">
	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content">
	            <div class="row">
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
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
                     <h5>organización</h5>
                     <div class="ibox-tools">
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                  </div>
                </div>
                <div class="ibox-content m-b-sm">
                        <div class="form-group">
                          <label class="col-lg-2 control-label"> Nombre <span required="" aria-required="true">*</span></label>
                          <div class="col-lg-10">
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="<?php echo set_value('nombre_empresa'); ?>">
                            <?php echo form_error('nombre', '<div class="error">', '</div>'); ?>
                          </div>
                        </div>
                        <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8"></div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                              <a href="<?php echo base_url().'usuarios/organizacion'?>" class="btn btn-default btn-block">Cancelar</a>
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

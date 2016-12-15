<div id="wrapper">

    <?php Template::cargar_vista('sidebar'); ?>

    <div id="page-wrapper" class="gray-bg row">

    <?php Template::cargar_vista('navbar'); ?>
    <div class="row border-bottom"></div>
    <?php  Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    <div class="col-lg-12">
        <div id="app_toast" class="wrapper-content">
            <toast :titulo="titulo" :mensaje="mensaje" :tipo="tipo" inline-template></toast>
            <div class="row">
                <!-- White Background -->
                <div class="col-lg-12">
                    <?php
                    $formAttr = array(
                      "method"        => "post",
                      "id"            => "perfilActualizar",
                      "autocomplete"  => "off"
                    );
                    echo form_open_multipart(base_url("administracion/guardar_password"), $formAttr);
                    ?>
                    <div class="ibox">
                        <div class="ibox-title"><h2>Bienvenido, <?php echo $usuario->nombre_completo; ?></h2></div>
                        <div class="ibox-content">
                            <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                      <label>Contrase&ntilde;a Actual</label>
                                    <input type="password" name="campo[current_password]" class="form-control" data-rule-required="true">
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                      <label>Contrase&ntilde;a</label>
                                    <input type="password" id="password" name="campo[password]" class="form-control" data-rule-required="true" data-rule-minlength="5" data-rule-maxlength="20">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                      <label>Confirmar Contrase&ntilde;a</label>
                                    <input type="password" name="campo[confirmar_password]" class="form-control" value="" data-rule-required="true" data-rule-minlength="5" data-rule-maxlength="20" data-rule-equalto="#password">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                                <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8"></div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 pull-right">
                                    <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
                                </div>

                        </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>

                <!-- White Background -->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox-float-e-margins">
                        <div class="ibox-title"><i class="fa fa-cogs" aria-hidden="true"> </i> Opciones</div>
                        <div class="ibox-content m-b-sm">
                            <ul>
                                <?php if(in_array(3,self::$ci->session->userdata('roles'))):?>
                                <li><a href="<?php echo base_url("usuarios/empresas_usuario"); ?>">Empresas</a></li>
        						<?php endif;?>
        						<?php if(in_array(2,self::$ci->session->userdata('roles'))):
        						?>
        							<li><a href="<?php echo base_url("usuarios/listar_empresa"); ?>">Empresas</a></li>
        						<?php endif;?>
                                <li><a href="<?php echo base_url("administracion/perfil"); ?>">Perfil</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox-float-e-margins">
                                <div class="ibox-title">nombre Usuario</div>
                                <div class="ibox-content m-b-sm"></div>
                            </div>
                        </div>
                    </div>
                </div> -->
            <div>
        </div>
    </div>

</div>
<!--<script></script>-->

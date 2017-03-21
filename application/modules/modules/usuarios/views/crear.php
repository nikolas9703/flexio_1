<div id="wrapper">
    
    <?php $this->load->view('include/sidebar'); ?>

    <div id="page-wrapper" class="gray-bg row">
    
    <?php $this->load->view('include/navbar'); ?>
    <div class="row border-bottom"></div>
    <?php  $this->load->view('include/breadcrumb', $breadcrumb); //Breadcrumb ?>

    <div class="col-lg-12">
        <div class="wrapper-content">
            <div class="row">
                <!-- White Background -->

                <div class="panel panel-default">
                    <div class="panel-heading"><h3 class="panel-title">Nuevo Usuario</h3></div>
                    <div class="panel-body">
                        
                        <?php
                        $formAttr = array(
                            'method'       => 'post', 
                            'id'           => 'createUsuarioForm',
                            'autocomplete' => 'off'
                        );
                        
                        echo form_open(base_url(uri_string()), $formAttr);
                        ?>
 
                        <div id="accordion" class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a href="#seccionInformacionPersonal" data-parent="#accordion" data-toggle="collapse" class="collapsed">Informacion Personal</a>
                                    </h5>
                                </div>
                                <div class="panel-collapse collapse in" id="seccionInformacionPersonal" style="height:auto;">
                                    <div class="panel-body">
                                        
                                        <!-- Inicia Campos: Informacion Personal -->
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label for="nombre">Nombre <span class="required">*</span></label>
                                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre">
                                                <?php echo form_error('nombre'); ?>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="apellido">Apellido <span class="required">*</span></label>
                                                <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Apellido">
                                                <?php echo form_error('apellido'); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label for="nombre">Tel&eacute;fono </label>
                                                <input type="text" id="telefono" name="telefono" value="<?php echo (!empty($usuario_info) && $usuario_info['telefono'] != "" ? $usuario_info['telefono'] : ""); ?>" class="form-control" placeholder="telefono">
                                             </div>
                                            <div class="form-group col-sm-6">
                                                <label for="apellido">Extensi&oacute;n  </label>
                                                <input type="text" id="extension" name="extension" value="<?php echo (!empty($usuario_info) && $usuario_info['extension'] != "" ? $usuario_info['extension'] : ""); ?>" class="form-control" placeholder="extension">
                                             </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label for="email">Email <span class="required">*</span></label>
                                                <input type="text" id="email" name="email" class="form-control" placeholder="Email">
                                                <?php echo form_error('email'); ?>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="id_rol">Rol <span class="required">*</span></label>
                                                <select id="id_rol" name="id_rol" class="form-control">
                                                    <option value="">Seleccione</option>
                                                    <?php
                                                    if(!empty($roles))
                                                    {
                                                        foreach ($roles AS $role) {
                                                            echo '<option value="'. $role['id'] .'">'. $role['nombre_rol'] .'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <?php echo form_error('id_rol'); ?>
                                            </div>
                                        </div>
                                        <!-- Termina Campos: Informacion Personal -->

                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a href="#seccionInformacionAcceso" data-parent="#accordion" data-toggle="collapse" class="collapsed">Informacion de Acceso</a>
                                    </h4>
                                </div>
                                <div class="panel-collapse collapse" id="seccionInformacionAcceso">
                                    <div class="panel-body">

                                    <!-- Inicia Campos: Informacion de Acceso -->
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <label for="usuario">Usuario <span class="required">*</span></label>
                                            <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Nombre de Usuario">
                                            <?php echo form_error('usuario'); ?>
                                        </div>
                                        
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <label for="password">Contrase&ntilde;a <span class="required">*</span></label>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Contrase&ntilde;a">
                                            <?php echo form_error('password'); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <label for="confirm_password">Re-Ingresar Contrase&ntilde;a <span class="required">*</span></label>
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-Ingresar Contrase&ntilde;a">
                                            <?php echo form_error('confirm_password'); ?>
                                        </div>
                                    </div>

                                    <div class="line-dashed"></div>
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <label class="text-danger">
                                                <input type="checkbox"> Enviarle un correo al usuario con su informaci&oacute;n de Acceso
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Termina Campos: Informacion de Acceso -->

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opcion si selecciona todas las agencias -->
                        <select id="todasAgencias" name="todasAgencias[]" class="hide" multiple="multiple">
                            <option value="">Seleccione</option>
                            <?php
                            if(!empty($agencias)){
                                foreach ($agencias AS $agencia) {
                                    echo '<option value="'. $agencia['id'] .'">'. $agencia['nombre_agencia'] .'</option>';
                                }
                            }
                            ?>
                        </select>
                        <div class="pull-right">
                            <a href="<?php echo base_url("usuarios/listar") ?>" class="btn btn-default">Cancelar</a>
                            <button id="saveFormBtn" type="button" class="btn btn-success">Guardar</button>
                        </div>

                        </form>

                    </div>
                </div>


                <!-- White Background -->
            </div>
        </div>
    </div>
    
</div>
<!--<script></script>-->

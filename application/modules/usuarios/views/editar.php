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

                <div class="panel panel-black">
                    <div class="panel-heading"><h3 class="panel-title">Editar Usuario</h3></div>
                    <div class="panel-body">
                        
                        <?php
                        $formAttr = array(
                            'method'       => 'post', 
                            'id'           => 'editUsuarioForm',
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
                                                <input type="text" id="nombre" name="nombre" value="<?php echo (!empty($usuario_info) && $usuario_info['nombre'] != "" ? $usuario_info['nombre'] : ""); ?>" class="form-control" placeholder="Nombre">
                                                <?php echo form_error('nombre'); ?>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="apellido">Apellido <span class="required">*</span></label>
                                                <input type="text" id="apellido" name="apellido" value="<?php echo (!empty($usuario_info) && $usuario_info['apellido'] != "" ? $usuario_info['apellido'] : ""); ?>" class="form-control" placeholder="Apellido">
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
                                                <input type="text" id="email" name="email" value="<?php echo (!empty($usuario_info) && $usuario_info['email'] != "" ? $usuario_info['email'] : ""); ?>" class="form-control" placeholder="Email">
                                                <?php echo form_error('email'); ?>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="id_rol">Rol <span class="required">*</span></label>
                                                <select id="id_rol" name="id_rol" class="form-control">
                                                    <option value="">Seleccione</option>
                                                    <?php
                                                    if(!empty($roles))
                                                    {
                                                        $defaultIdRol = (!empty($usuario_info) && $usuario_info['id_rol'] != "" ? $usuario_info['id_rol'] : "");
                                                        foreach ($roles AS $role) 
                                                        {
                                                            $selected = ($role['id'] == $defaultIdRol ? 'selected="selected"' : "");
                                                            echo '<option value="'. $role['id'] .'" '. $selected .'>'. $role['nombre_rol'] .'</option>';
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
                                        <a href="#seccionAgencias" data-parent="#accordion" data-toggle="collapse">Agencias</a>
                                    </h4>
                                </div>
                                <div class="panel-collapse collapse" id="seccionAgencias" style="height:auto;">
                                    <div class="panel-body">
                                     
                                        <!-- Inicia Campos: Agencias -->
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label>
                                                  <input type="checkbox" id="asignarTodasAgencias" name="asignarTodasAgencias" <?php echo (!empty($usuario_info['agencias']) && is_string($usuario_info['agencias']) ? 'checked="checked"' : ""); ?> /> Asignarle todas las Agencias y departamentos.
                                                </label>
                                            </div>
                                            <div class="form-group col-sm-6"></div>
                                        </div>

                                        <div class="table-responsive" style="overflow: unset !important">
                                            <table class="table table-striped tablaAgencias col-md-12">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Agencia <span class="required">*</span></th>
                                                        <th class="text-center">Departamento <span class="required">*</span></th>
                                                        <th class="text-center">Celula</th>
                                                        <th class="text-center">&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        if(!empty($usuario_info['agencias']) && is_array($usuario_info['agencias']))
                                                        {
                                                            foreach ($usuario_info['agencias'] AS $index => $currentAgencia)
                                                            {
                                                                $id_row          = 'id="agencia'.$index.'"';
                                                                $id_agencia      = "id_agencia_".$index;
                                                                $id_departamento = "id_departamento_".$index;
                                                                $id_celula       = "id_celula_".$index;
                                                                $delete_link     = '<a href="#" class="btn btn-danger btn-block deleteAgencia'. $index .'" data-index="'. $index .'"><i class="fa fa-trash"></i> <span class="hidden-sm">Eliminar</span></a>';
                                                                //$delete_link     = ($index > 0 ? '<a href="#" class="btn btn-danger btn-block deleteAgencia'. $index .'" data-index="'. $index .'"><i class="fa fa-trash"></i> <span class="hidden-sm">Eliminar</span></a>' : '');
                                                                ?>
                                                                <tr <?php echo $id_row; ?> class="default">
                                                                    <td width="30%">
                                                                        <select id="<?php echo $id_agencia; ?>" name="agencia[<?php echo $index; ?>][id]" class="form-control">
                                                                            <option value="">Seleccione</option>
                                                                            <?php
                                                                            if(!empty($agencias))
                                                                            {
                                                                                $defaultIdAgencia = (!empty($currentAgencia) ? $currentAgencia['id_agencia'] : ""); 
                                                                                foreach ($agencias AS $agencia)
                                                                                {
                                                                                    $selected = ($agencia['id'] == $defaultIdAgencia ? 'selected="selected"' : "");
                                                                                    echo '<option value="'. $agencia['id'] .'" '.$selected.'>'. $agencia['nombre_agencia'] .'</option>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                    <td width="30%">
                                                                        <select id="<?php echo $id_departamento; ?>" name="agencia[<?php echo $index; ?>][id_departamento][]" size="1" class="form-control" multiple="multiple" data-placeholder="Seleccione">
                                                                            <?php
                                                                               $departamentos = $this->departamentos->get_departamentos($currentAgencia['id_agencia']);

                                                                               if(!empty($departamentos))
                                                                                {
                                                                                    $defaultDeparmentosArr = !empty($currentAgencia['departamentos']) ? $currentAgencia['departamentos'] : array();

                                                                                    //Recorrer arreglo de departamentos
                                                                                    foreach ($departamentos AS $departamento)
                                                                                    {
                                                                                        //Marcar como seleccionado los departamentos, que coinciden.
                                                                                        $selected = in_array($departamento['id'], $defaultDeparmentosArr) ? 'selected="selected"' : "";
                                                                                        
                                                                                        echo '<option value="'. $departamento['id'] .'" '. $selected .'>'. $departamento['nombre_departamento'] .'</option>';
                                                                                    }
                                                                                } 
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                    <td width="30%">
                                                                        <?php $celulas = $this->celulas->get_celulas_by_agencia($currentAgencia['id_agencia']); ?>
                                                                        <div class="celula-field <?php echo (!empty($celulas) ? "show" : "hide") ?>">
                                                                            <select id="<?php echo $id_celula; ?>" name="agencia[<?php echo $index; ?>][id_celula][]" class="form-control" size="1" multiple="multiple" data-placeholder="Seleccione">
                                                                                <?php
                                                                                   if(!empty($celulas))
                                                                                    {
                                                                                        $defaultCelulasArr = !empty($currentAgencia['celulas']) ? $currentAgencia['celulas'] : array();

                                                                                        //Recorrer arreglo de celulas
                                                                                        foreach ($celulas AS $celula)
                                                                                        {
                                                                                            //Marcar como seleccionado la celulas, que coinciden.
                                                                                            $selected = in_array($celula['id'], $defaultCelulasArr) ? 'selected="selected"' : "";
                                                                                            
                                                                                            echo '<option value="'. $celula['id'] .'" '. $selected .'>'. $celula['nombre_celula'] .'</option>';
                                                                                        }
                                                                                    } 
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td width="15%"><?php echo $delete_link; ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                             <tr id="agencia0" class="default">
                                                                <td width="30%">
                                                                    <select id="id_agencia_0" name="agencia[0][id]" class="form-control default-agencia-group">
                                                                        <option value="">Seleccione</option>
                                                                        <?php
                                                                        if(!empty($agencias))
                                                                        {
                                                                            foreach ($agencias AS $agencia) {
                                                                                echo '<option value="'. $agencia['id'] .'">'. $agencia['nombre_agencia'] .'</option>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td width="30%">
                                                                    <select id="id_departamento_0" name="agencia[0][id_departamento][]" size="1" class="form-control default-agencia-group" multiple="multiple" disabled="disabled" data-placeholder="Seleccione"></select>
                                                                </td>
                                                                <td width="30%">
                                                                    <div class="celula-field hide">
                                                                        <select id="id_celula" name="agencia[0][id_celula][]" class="form-control" disabled="disabled" size="1" multiple="multiple" disabled="disabled" data-placeholder="Seleccione"></select>
                                                                    </div>
                                                                </td>
                                                                <td width="15%"></td>
                                                            </tr>
                                                            <?php 
                                                        }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="agencia-error"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <a href="#" id="agregarAgencia" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Agencia</a>
                                        <!-- Termina Campos: Agencias -->

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
                                            <input type="text" id="usuario" name="usuario" value="<?php echo (!empty($usuario_info) && $usuario_info['usuario'] != "" ? $usuario_info['usuario'] : ""); ?>" class="form-control" placeholder="Nombre de Usuario">
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
                        <input type="hidden" id="idAgencia" name="idAgencia" value="" />
                        <input type="hidden" id="indexAgencia" anme="indexAgencia" value="" />
                        <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>" />
                        <div class="pull-right">
                            <a href="<?php echo base_url("usuarios/listar") ?>" class="btn btn-default">Cancelar</a>
                            <button id="saveFormBtn" type="button" class="btn btn-primary">Guardar</button>
                        </div>

                        </form>

                    </div>
                </div>


                <!-- White Background -->
            </div>
        </div>
    </div>
    
</div>


<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Opciones</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<?php 
function findKey($keySearch, $array) {
    foreach ($array AS $key => $item){
        if(!empty($item[$keySearch])){
            return $key;
        }
    }
    return false;
}

/*if(!empty($modules))
{
    foreach ($modules AS $controller_name => $module)
    {
        $module_id = (!empty($module['module_id']) ? $module['module_id'] : "");

        $selectedModuleKey  = findKey($controller_name, $rol_info['modulos']);
        $selectedModule = !empty($rol_info['modulos'][$selectedModuleKey]) && !empty($rol_info['modulos'][$selectedModuleKey][$controller_name]) ? $rol_info['modulos'][$selectedModuleKey][$controller_name] : array();

        $cntr = 0;
        $total = count($module['resources']);
        $permissions = $module['permissions'];
        foreach ($module['resources'] AS $resource) 
        {
            $resource_id = $resource['id'];
            $resource_name = $resource['resource_name'];
            $resource_name  = $resource_url = str_replace("/(:num)", "", $resource_name);

            $selectedResource = (!empty($selectedModule['resources']) && !empty($selectedModule['resources'][$resource['resource_name']]) ? $selectedModule['resources'][$resource['resource_name']] : "");


            if(!empty($permissions))
            {
                foreach ($permissions AS $index => $permission_alias)
                {
                    $fieldId = uniqid(rand(), true); //Generate a random, unique, alphanumeric string.
                    $selectedPermission = (!empty($selectedResource) && in_array($index, $selectedResource) ? 'checked="checked"' : ""); 
                    $selectedPermissionClass = (!empty($selectedResource) && in_array($index, $selectedResource) ? 'remove_perm' : ""); 


                        echo $resource_name." :".$selectedPermission."<br>";
                    

                }
            }
        }
    }
}
echo "<pre>";
print_r($rol_info);
echo "</pre>";
die();*/
?>
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
                    <div class="panel-heading">

                        <span class="panel-title">
                            <i class="fa fa-info-circle"></i>
                            <span class="hidden-xs hidden-sm">Nuevo Rol</span>
                        </span>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">Informacion del Rol</a></li>
                            <li><a href="#tab2" data-toggle="tab">Permisos</a></li>
                        </ul>

                    </div>
                    <div class="panel-body">

                        <?php
                        $formAttr = array(
                            'method'        => 'post', 
                            'id'            => 'roleForm',
                            'autocomplete'  => 'off'
                        );

                        echo form_open(base_url(uri_string()), $formAttr);
                        ?>
                        <div class="tab-content">
                            
                            <div class="tab-pane active" id="tab1">

                                <div class="form-group col-sm-12">
                                    <label for="role_name">Nombre del Rol <span class="required">*</span></label>
                                    <input type="text" id="role_name" name="role_name" value="<?php echo !empty($rol_info) && $rol_info['nombre_rol'] != "" ? $rol_info['nombre_rol'] : ''; ?>" class="form-control" placeholder="Introduzca Nombre del Rol" />
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="role_description">Descripcion</label>
                                    <textarea id="role_desciption" name="role_description" class="form-control" rows="2" placeholder="Introduzca Descripcion del Rol"><?php echo !empty($rol_info) && $rol_info['descripcion_rol'] != "" ? $rol_info['descripcion_rol'] : ''; ?></textarea>
                                </div>
                               
                            </div>
                            <div class="tab-pane" id="tab2">

                                <p>Asigne los permisos al modulo o modulos que le quiera dar acceso a este rol.</p>

                                <!-- BEGIN ACORDEON -->
                                <div id="accordion" class="panel-group">
                                    <?php 
                                        if(!empty($modules)):
                                            foreach ($modules AS $controller_name => $module):
                                                $module_id = (!empty($module['module_id']) ? $module['module_id'] : "");

                                                $selectedModuleKey  = findKey($controller_name, (!empty($rol_info['modulos']) ? $rol_info['modulos'] : array()));
                                                $selectedModule = !empty($rol_info['modulos']) && !empty($rol_info['modulos'][$selectedModuleKey]) && !empty($rol_info['modulos'][$selectedModuleKey][$controller_name]) ? $rol_info['modulos'][$selectedModuleKey][$controller_name] : array();
                                                
                                    ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5 class="panel-title">
                                                <a href="#collapse-<?php echo $controller_name; ?>" data-parent="#accordion" data-toggle="collapse" class="collapsed"><i class="fa fa-minus-circle"></i> Modulo: <?php echo ucwords($module['module_name']); ?></a>
                                            </h5>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapse-<?php echo $controller_name; ?>" style="height: 0px;">
                                            <div class="panel-body">
                                            
                                                <?php echo (!empty($module['module_description']) ? '<p>'.$module['module_description'].'</p>' : ""); ?>
                                                
                                                <?php if(!empty($module['resources'])): ?>

                                                <div class="table-responsive">
                                                <table class="table table-bordered responsive">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2" width="20%">Seccion</th>
                                                            <th rowspan="2" width="50%">URL</th>
                                                            <th align="center" colspan="<?php echo count($module['permissions']) ?>" width="30%">Permisos</th>
                                                        </tr>
                                                        <tr>
                                                        <?php 
                                                        if(!empty($module['permissions']))
                                                        {
                                                            foreach ($module['permissions'] AS $index => $permission_alias)
                                                            {
                                                                echo '<th align="center">'. $permission_alias .'</th>';
                                                            }
                                                        }
                                                        ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $cntr = 0;
                                                        $total = count($module['resources']);
                                                        $permissions = $module['permissions'];
                                                        foreach ($module['resources'] AS $resource) 
                                                        {
                                                            $module_name_alias = str_replace(" - ", "_", strtolower(trim($module['module_name'])));
                                                            $module_name_alias = str_replace(" ", "_", $module_name_alias);

                                                            $resource_id = $resource['id'];
                                                            $resource_name = $resource['resource_name'];
                                                            $resource_name  = $resource_url = str_replace("/(:num)", "", $resource_name);
                                                            $resource_name = str_replace(strtolower($module['module_name']), "", $resource_name);
                                                            $resource_name = str_replace($module_name_alias, "", $resource_name);
                                                            $resource_name = $resourceName = str_replace("/", "", $resource_name);
                                                            $resource_name  = str_replace("-", " ", $resource_name);
                                                            $module_name    = ($resource_name == "listar" ? plural($module['module_name']) : singular($module['module_name']));
                                                            $resource_name  = ucwords($resource_name) ." ". ucwords(str_replace("vfx - ", "", $module_name));

                                                            $selectedResource = (!empty($selectedModule['resources']) && !empty($selectedModule['resources'][$resource['resource_name']]) ? $selectedModule['resources'][$resource['resource_name']] : "");

                                                            $row = '<tr>
                                                            <td>'. $resource_name .'</td>
                                                            <td>'. base_url($resource_url) .'</td>';
 
                                                            if(!empty($permissions))
                                                            {
                                                                foreach ($permissions AS $index => $permission_alias)
                                                                {
                                                                    $fieldId = uniqid(rand(), true); //Generate a random, unique, alphanumeric string.
                                                                    $selectedPermission = (!empty($selectedResource) && in_array($index, $selectedResource) ? 'checked="checked"' : ""); 
                                                                    $selectedPermissionClass = (!empty($selectedResource) && in_array($index, $selectedResource) ? 'remove_perm' : ""); 
                                                                    $fieldName = "module[$module_id][resource][$resource_id][$index]";
                                                                    
                                                                    if (preg_match("/_/i", $index))
                                                                    {
                                                                        //Verficar si el nombre del route concuerda con el nombre de permiso
                                                                        if (preg_match("/".$resourceName."_/is", $index))
                                                                        {
                                                                            $row .= '<td align="center"><div class="checkbox checkbox-success"><input type="checkbox" name="'. $fieldName .'" id="'. $fieldId .'" class="'. $selectedPermissionClass .'" '. $selectedPermission .' /><label for="'. $fieldId .'">&nbsp;</label></div></td>';
                                                                        }
                                                                        else
                                                                        {
                                                                            $row .= '<td align="center" style="background-color: #fcfcfc;">&nbsp;</td>';
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                       $row .= '<td align="center"><div class="checkbox checkbox-success"><input type="checkbox" name="'. $fieldName .'" id="'. $fieldId .'" class="'. $selectedPermissionClass .'" '. $selectedPermission .' /><label for="'. $fieldId .'">&nbsp;</label></div></td>';
                                                                    }
                                                                }
                                                            }

                                                            $row .= '</tr>';
                                                            echo $row;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                </div>

                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                            endforeach;
                                        endif;
                                    ?>
                                </div>

                                <!-- END ACORDEON -->
                                <input type="hidden" id="id_rol" name="id_rol" value="<?php echo (!empty($id_rol) ? $id_rol : ""); ?>" />
                                <div class="pull-right">
                                    <a href="<?php echo base_url("roles/listar") ?>" class="btn btn-default">Cancelar</a>
                                   <button type="submit" class="btn btn-primary "><i class="fa fa-save"></i>&nbsp;Guardar</button>
                                </div>

                            </div>
   
                        </div>
                        </form>

                        <form id="deletePermisoForm" class="hide">
                            <input type="text" id="permiso" name="" />
                            <input type="hidden" name="id_rol" value="<?php echo (!empty($id_rol) ? $id_rol : ""); ?>" />
                        </form>
                    </div>
                </div>


                <!-- White Background -->
            </div>
        </div>
    </div>
    
</div>
<!--<script></script>-->

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
                <?php
                    $formAttr = array(
                        'method' => 'POST',
                        'id' => 'CrearEndososForm',
                        'autocomplete' => 'off',
                        'enctype' => 'multipart/form-data'
                    );
                echo form_open(base_url('endosos/guardar'), $formAttr);
                ?>
                <div class="ibox">
                    <!-- Tab panes -->
                    <div class="ibox-content" style="display: block;">
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Ramo:</label>
                                <select id="id_ramo" name="campos[id_ramo]" class="form-control" onchange="verpolizas()">
                                    <option value="">Seleccione</option>
                                    <?php
                                        if(!empty($menu_crear)){
                                            $cont = 0;
                                            foreach ($menu_crear as  $value) {
                                                foreach ($menu_crear AS $menu) {
                                                    if ($value['id'] == $menu['padre_id']) {
                                                        $cont++;
                                                    }
                                                }
                                                if($cont == 0 && $value['padre_id'] != 0 && $value['estado'] && in_array($value['id'], $rolesArray) && in_array($value['id'], $usuariosArray) ){
                                                    echo '
                                                    <option value="'.$value['id'].'" :selected="'.$value['id'].' == id_ramo" >'.$value['nombre'].'</option>
                                                    ';
                                                }
                                                $cont = 0;
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-5 col-lg-5">
                                <label>Cliente: </label>
                                <select id="cliente_id" name="campos[cliente_id]" class="form-control" onchange="verpolizas()">
                                    <option value="">Seleccione</option>
                                    <?php
                                        if(!empty($clientes)){
                                            foreach ($clientes as $key => $value) {
                                                echo'
                                                <option value="'.$value->id.'" :selected=" '.$value->id.' == id_cliente">'.$value->nombre.' - '.$value->identificacion.'</option>
                                                ';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <label>Poliza: <span required="" aria-required="true">*</span></label>
                                <select id="id_poliza" name="campos[id_poliza]" class="form-control" data-rule-required="true">
                                    <option value="">Seleccione</option>
                                    <option v-for="pol in poliza" value="{{pol.id}}" :selected="pol.id == id_poliza">{{pol.numero}}</option>
                                </select>
                            </div>
                        </div>
                        <?php echo modules::run('endosos/ocultoformulario')?>
                    </div>
                </div>    
                <?php echo form_close(); ?>
            </div>
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
?>
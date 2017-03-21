<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); ?>


        <div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">
                    <div id="mensaje">
                    </div>
                </div>
                <div ng-controller="toastController"></div>

                <div class="ibox">
                    <!-- Tab panes -->
                    <div class="ibox-content" >

                    <?php
                        $formAttr = array(
                            'method' => 'POST',
                            'id' => 'crearRemesaForm',
                            'autocomplete' => 'off'
                        );
                        echo form_open(base_url(uri_string()), $formAttr);
                    ?>
                        <div class="row">

                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                    <label>Aseguradora</label>
                                    <select name="aseguradora" id="aseguradora" class="form-control">
                                        <option value="">Seleccione</option>
                                        <?php
                                            foreach ($aseguradoras as $key => $value) {
                                                echo '
                                                <option value="'.$value->id.'">'.$value->nombre.'</option>
                                                ';
                                            }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <label>Rango de fechas</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                                        <input type="input" id="fecha_desde" name="fecha_desde" readonly="readonly" class="form-control" value="" data-rule-required="true">
                                        <span class="input-group-addon">a</span>
                                        <input type="input" id="fecha_hasta" name="fecha_hasta" readonly="readonly" class="form-control" value="" data-rule-required="true">
                                    </div>
                                </div>
                        
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                    <label>Ramo</label>
                                    <select id="ramos" name="ramos" class="ramo chosen-select grouper" multiple="multiple" data-placeholder="Seleccione una opción">
                                        <option value="todos">Todos</option>
                                        <?php 
                                            $cont = 0;
                                            foreach ($menu_crear as  $value) {
                                                foreach ($menu_crear AS $menu) {
                                                    if ($value['id'] == $menu['padre_id']) {
                                                        $cont++;
                                                    }
                                                }
                                                if($cont == 0 && $value['padre_id'] != 0 && $value['estado'] && in_array($value['id'], $rolesArray) && in_array($value['id'], $usuariosArray) ){
                                                    echo '
                                                    <option value="'.$value['id'].'">'.$value['nombre'].'</option>
                                                    ';
                                                }
                                                $cont = 0;
                                            }
                                        ?>
                                    </select>

                                    <select id="ramos2" name="ramos2" class="hidden" multiple="multiple" data-placeholder="Seleccione una opción">
                                        <option value="todos">Todos</option>
                                        <?php 
                                            $cont = 0;
                                            foreach ($menu_crear as  $value) {
                                                foreach ($menu_crear AS $menu) {
                                                    if ($value['id'] == $menu['padre_id']) {
                                                        $cont++;
                                                    }
                                                }
                                                if($cont == 0 && $value['padre_id'] != 0 && $value['estado'] && in_array($value['id'], $rolesArray) && in_array($value['id'], $usuariosArray) ){
                                                    echo '
                                                    <option value="'.$value['id'].'">'.$value['nombre'].'</option>
                                                    ';
                                                }
                                                $cont = 0;
                                            }
                                        ?>
                                    </select>

                                </div>

                            </div>

                            <input type="hidden" id="vista" name="vista" value="<?php echo $vista?>">
                        </div>
                        <div class="row">
                            <div class="col-xs-0 col-sm-0 col-md-9 col-lg-9">&nbsp;</div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                <input type="button" id="clearBtn" class="btn btn-success btn-block" value="Actualizar" @click="getRemesas(1,0)" />
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                    </div>
                </div>

                <?php  echo modules::run('remesas/tabla_remesas')?>

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
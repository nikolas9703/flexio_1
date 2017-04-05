<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); ?>

        <div class="col-lg-12">
            <div class="wrapper-content-1">
                <div class="row">
                    <div id="mensaje">
                    </div>
                </div>
                <div ng-controller="toastController"></div>
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
                                                    if($id_ramo_endoso == $value['id']){
                                                        echo '
                                                            <option value="'.$value['id'].'" selected>'.$value['nombre'].'</option>
                                                        ';
                                                    }else{
                                                        echo '
                                                            <option value="'.$value['id'].'">'.$value['nombre'].'</option>
                                                        ';
                                                    }
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
                                                if($id_cliente_endoso == $value->id){
                                                    echo'
                                                        <option value="'.$value->id.'" selected>'.$value->nombre.' - '.$value->identificacion.'</option>
                                                    ';
                                                }else{
                                                    echo'
                                                        <option value="'.$value->id.'">'.$value->nombre.' - '.$value->identificacion.'</option>
                                                    ';
                                                }
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
                        <?php echo modules::run('endosos/ocultoformulario',$data)?>
                    </div>
                </div>    
                <?php echo form_close(); ?>
            </div>
            <div class="wrapper-content">
                <div class="row" id="subPanels">
                    <?php
                        SubpanelTabs::visualizar($subpanels);
                    ?>
                </div>
            </div>
            <div class="row">
                <?php echo modules::run('endosos/comentaformulario',$campos); ?>
            </div>    
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
echo Modal::config(array(
    "id" => "optionsModal",
    "size" => "sm"
))->html();

echo Modal::config(array(
    "id" => "documentosModal",
    //"size" => "md",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("endosos/formularioModal")
))->html();

echo Modal::config(array(
    "id" => "documentosModalEditar",
    "size" => "md",
    "titulo" => "Cambiar nombre del documento",
    "contenido" => modules::run("endosos/formularioModalEditar")
))->html();


$formAttr = array('method' => 'POST', 'id' => 'exportarDocumentos','autocomplete'  => 'off');
echo form_open(base_url('documentos/exportarDocumentos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_documentos" value="" />
<?php
echo form_close();


$formAttr = array('method' => 'POST', 'id' => 'exportarEndosos', 'autocomplete' => 'off');
    echo form_open(base_url('endosos/exportar'), $formAttr);
?>
    <input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>


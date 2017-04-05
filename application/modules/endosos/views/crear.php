<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content-1">
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
                                <select id="id_ramo" name="campos[id_ramo]" class="form-control id_ramo" ><!-- onchange="verpolizas(1)" -->
                                    <option value=''>Seleccione</option>
                                    <option v-for="ramos in ramos" value="{{ramos.id}}" :selected="ramos.id == id_ramo">{{ramos.nombre}}</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-5 col-lg-5">
                                <label>Cliente: </label>
                                <select id="cliente_id" name="campos[cliente_id]" class="form-control cliente_id" ><!-- onchange="verpoliza(2)"-->
                                    <option value=''>Seleccione</option>
                                    <option v-for="cli in clientes" value="{{cli.id}}" :selected="cli.id == id_cliente">{{cli.nombre+' - '+cli.identificacion}}</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <label>Poliza: <span required="" aria-required="true">*</span></label>
                                <select id="id_poliza" name="campos[id_poliza]" class="form-control id_poliza" data-rule-required="true" ><!-- onchange="verpoliza(3)" -->
                                    <option value=''>Seleccione</option>
                                    <option v-for="pol in poliza" value="{{pol.id}}" :selected="pol.id == id_poliza">{{pol.numero}}</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="campos[detalle_unico_endoso]" id="detalle_unico_endoso">
                        <?php echo modules::run('endosos/ocultoformulario',$data)?>
                    </div>
                </div>    
                <?php echo form_close(); ?>
            </div>
            <div class="wrapper-content">
                <div class="row" id="subPanels">
                    <?php SubpanelTabs::visualizar($subpanels); ?>
                </div>
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
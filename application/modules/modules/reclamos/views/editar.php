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
                <div class="row">                           
                    <div class="filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">

                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <label>Empezar reclamo desde</label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <select id="formulario_tipo" name="campo[tipo]" class="white-bg form-control" role="tablist" >
                                <option value="polizas">Pólizas</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-5 col-md-5 col-lg-5">
                            <select id="poliza_seleccionado" class="white-bg form-control" role="tablist" name="campo[poliza]"  onchange="buscaPoliza()">
                                <option value="">Seleccione</option>
                                <option v-for="poliza in polizas | orderBy 'nombre'" v-bind:value="poliza.id" :selected="poliza.id == polizaid">
                                    {{ poliza.cliente + ' - ' + poliza.aseguradora + ' - ' + poliza.numero}}
                                </option>
                            </select>                            
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>

                        <!-- Hide Nav-Tabs -->

                    </div> 
                    <?php
                    echo modules::run("reclamos/ocultoformulario", $id_ramo, $tipo_interes);
                    ?> 

                    <div class="row">
                        <?php
                        //echo modules::run('reclamos/tabladetalles');
                        SubpanelTabs::visualizar($subpanels);
                        ?>
                    </div>
                    <div class="row">
                        <?php echo modules::run('reclamos/comentariosformulario'); ?>
                    </div>

                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<!-- 
    Modal de coberuras y deducciones 
-->
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div_coberturas" style="display:none;">

    <div class="row">
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <label></label>
        </div>
        <div class="col-xs-11 col-sm-6 col-md-6 col-lg-6">
            <label>Coberturas</label>    
        </div>
        <div class="col-xs-11 col-sm-5 col-md-5 col-lg-5">
            <label>Valor</label>    
        </div>
    </div>
    
    <div class="row" v-for="find in coberturasInfo.coberturas" track-by="$index">
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <input type="checkbox" value="{{find.id}}" name="cobertura_poliza[]" id="cobertura_poliza[]" class="form-control" style="width: 50%;">
        </div>
        <div class="col-xs-11 col-sm-6 col-md-6 col-lg-6">
            <input type="text" v-model="find.cobertura" class="form-control" disabled="disabled">
        </div>
        <div class="col-xs-11 col-sm-5 col-md-5 col-lg-5">
            <input type="text" v-model="find.valor_cobertura" class="form-control" disabled="disabled">
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <label></label>
        </div>
        <div class="col-xs-11 col-sm-6 col-md-6 col-lg-6">
            <label>Deducible</label>    
        </div>
        <div class="col-xs-11 col-sm-5 col-md-5 col-lg-5">
            <label>Valor</label>    
        </div>
    </div>
    
    <div class="row" v-for="find in coberturasInfo.deducion" track-by="$index">
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <input type="checkbox" value="{{find.id}}" name="deduccion_poliza[]" id="deduccion_poliza_{{$index}}" class="form-control deduccion_poliza" style="width: 50%;" >
        </div>
        <div class="col-xs-11 col-sm-6 col-md-6 col-lg-6">
            <input type="text" v-model="find.deduccion" class="form-control" disabled="disabled">
        </div>
        <div class="col-xs-11 col-sm-5 col-md-5 col-lg-5">
            <input type="text" v-model="find.valor_deduccion" id="valor_deduccion_{{$index}}" class="form-control" disabled="disabled">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <button id="closeModalCoberturas" class="btn btn-w-m btn-default btn-block" type="button" >Cancelar</button>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <button id="guardarCoberturasPolizas" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button>

        </div>
    </div>
</div>

<!-- 
    Termina Modal de coberuras y deducciones 
-->
<?php
echo Modal::config(array(
    "id" => "verCoberturas",
    "size" => "lg"
))->html();

echo Modal::config(array(
    "id" => "optionsModal",
    "size" => "sm"
))->html();

$formAttr = array('method' => 'POST', 'id' => 'exportarDocumentos', 'autocomplete' => 'off');
echo form_open(base_url('reclamos/exportarDocumentos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_documentos" value="" />
<?php echo form_close();


echo Modal::config(array(
    "id" => "documentosModal",
    //"size" => "md",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("reclamos/formularioModal")
))->html();

echo Modal::config(array(
    "id" => "documentosModalEditar",
    "size" => "md",
    "titulo" => "Cambiar nombre del documento",
    "contenido" => modules::run("reclamos/formularioModalEditar")
))->html();

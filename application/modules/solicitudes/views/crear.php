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
                            <label>Empezar solicitud desde</label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <select id="formulario_tipo" name="campo[tipo]" class="form-control" @change="seleccionarCliente()">
                                <option value="seleccione">Seleccione</option>
                                <option v-for="tipo in catalogoClientes" v-bind:value="tipo.valor">
                                         {{{ tipo.etiqueta}}}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <select id="cliente_seleccionado" class="form-control" name="campo['cliente]" id="cliente" :disabled="disabledOpcionClientes" @change="clienteInfoSelect()">
                        <option value="">Seleccione</option>
                        <option v-for="cliente in clientes | orderBy 'nombre'" v-bind:value="cliente.id">
                          {{ cliente.nombre + ' - ' + cliente.identificacion}}
                        </option>
                            </select>                            
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>

                        <!-- Hide Nav-Tabs -->
                        
                    </div>                
                    <?php
                    echo modules::run("solicitudes/ocultoformulario");
                    ?>                    
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div_coberturas" style="display:none;">
<div class="col-xs-12 col-sm-3 col-md-6 col-lg-6">
<label>Coberturas</label>    
</div>
<div class="col-xs-12 col-sm-3 col-md-6 col-lg-4">
<label>Deducible</label>    
</div>
<div v-for="find in coberturasInfo" track-by="$index">
<div class="col-xs-12 col-sm-3 col-md-6 col-lg-6">
<input type="text" name="coberturas[]" v-model="find.nombre" class="form-control" id="coberturas_nombre" disabled>
</div>
<div class="col-xs-12 col-sm-3 col-md-6 col-lg-4">
<input type="text" name="deducibles[]" value="" class="form-control" id="coberturas_deducibles">
</div>
<div class="col-xs-12 col-sm-3 col-md-6 col-lg-1">
<button class="btn btn-default btn-block" @click="removeCampos(find)"><i class="fa fa-trash"></i></button>
</div>
</div>
<div class="col-xs-12 col-sm-3 col-md-6 col-lg-1">
<button class="btn btn-default btn-block" @click="addCampos"><i class="fa fa-plus"></i></button>
</div>
</div>
<div class="row botones_coberturas" style="display:none;">
<div class="form-group col-xs-12 col-sm-6 col-md-6">
<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
</div>
<div class="form-group col-xs-12 col-sm-6 col-md-6">
<button id="guardarCoberturas" class="btn btn-w-m btn-primary btn-block" data-dismiss="modal" type="button">Guardar</button>
</div>
</div> 
<?php
echo    Modal::config(array(
    "id"    => "verCoberturas",
    "size"  => "lg"
))->html();

echo    Modal::config(array(
    "id"    => "optionsModal",
    "size"  => "sm"
))->html();


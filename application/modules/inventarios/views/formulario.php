<!--panel de datos generales-->
<div class="ibox">


    <nav class="navbar-default navbar-static" id="navbar-example2"
         style="margin-bottom:-3px;z-index:9999;top:0;background: #F3F3F4;width: 100%;">
        <div class="container-fluid" style="padding:0px;">
            <div class="collapse navbar-collapse bs-example-js-navbar-scrollspy" style="padding:0px;">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#datos_generales"> Datos generales</a></li>
                    <li class=""><a href="#atributos"> Atributos</a></li>
                    <li class=""><a href="#precios_venta"> Precio(s) de venta</a></li>
                    <li class=""><a href="#tarifas_alquiler"> Tarifas de alquiler</a></li>
                    <li class=""><a href="#impuestos"> Impuestos</a></li>
                    <li class=""><a href="#cuentas"> Cuentas</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <detalle :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos"></detalle>

    <dato-adicional></dato-adicional>


    <div class="ibox-title border-bottom" id="atributos">
        <h5>Atributos</h5>
    </div>

    <div class="ibox-content" style="border: 0px; display: block;">

        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive" style="padding-bottom: 100px;">
                    <table id="atributosTable" class="table table-noline tabla-dinamica" style="display: block;">

                        <thead>
                        <tr>
                            <th width="48%" style="background: white;color: #555;"> Nombre</th>
                            <th width="48%" style="background: white;color: #555;"> Descripci&oacute;n</th>
                            <th width="1%" style="background: white;color: #555;"></th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr id="atributos{{$index}}" v-for="atributo in detalle.atributos">
                            <td>
                                <input type="text" name="atributos[{{$index}}][nombre]" class="form-control"
                                       v-model="atributo.nombre" :disabled="config.disableDetalle">
                            </td>
                            <td>
                                <input type="text" name="atributos[{{$index}}][descripcion]" class="form-control"
                                       v-model="atributo.descripcion" :disabled="config.disableDetalle">
                            </td>
                            <td>
                                <button type="button" class="btn btn-default btn-block" v-if="$index == 0"
                                        @click="addAttribute()"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-default btn-block" v-if="$index != 0"
                                        @click="removeAttribute(atributo)"><i class="fa fa-trash"></i></button>
                                <input type="hidden" name="atributos[{{$index}}][id]" class="form-control"
                                       v-model="atributo.id">
                            </td>
                        </tr>
                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>


    <div class="ibox-title border-bottom" id="precios_venta">
        <h5>Precios de venta</h5>
    </div>

    <div class="ibox-content" style="border: 0px; display: block;">

        <div class="row">

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3" v-for="precio_venta in catalogos.precios_venta"
                 :style="{clear: ($index%4 == 0) ? 'both' : 'none' }">
                <label>
                    {{precio_venta.nombre}}
                    <span class="label label-info" v-if="precio_venta.principal == 1">Default</span>
                </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                    <input type="text" name="precios[{{$index}}][precio]" class="form-control" style="width:100%;"
                           v-model="detalle.precios[$index].precio" v-inputmask="detalle.precios[$index].precio"
                           :config="config.inputmask.currency2" :disabled="config.disableDetalle">
                    <input type="hidden" name="precios[{{$index}}][id_precio]" v-model="precio_venta.id">
                </div>
            </div>

        </div>

    </div>


    <div class="ibox-title border-bottom" id="tarifas_alquiler">
        <h5>Tarifas de alquiler o de contrato recurrente</h5>
        <div class="onoffswitch" style="margin-left:304px; height:20px;">
            <input type="checkbox" name="campo[item_alquiler]" class="onoffswitch-checkbox" id="myonoffswitch"
                   v-model="detalle.item_alquiler">
            <label class="onoffswitch-label" for="myonoffswitch" style="height:20px;">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch" style="height:20px;"></span>
            </label>
        </div>
    </div>

    <div class="ibox-content" style="border: 0px; display: block;">

        <div class="row">

            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <!--<li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> This is tab</a></li>
                    <li class="">          <a data-toggle="tab" href="#tab-2" aria-expanded="false">This is second tab</a></li>-->
                    <li v-for="precio in catalogos.precios_alquiler" :class="{active: $index == 0 }">
                        <a data-toggle="tab" href="#tab-{{precio.id}}">{{ precio.nombre }} <span
                                v-if="precio.principal === 1 " class="label label-warning">Default</span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-{{precio.id}}" class="tab-pane" :class="{ active: $index == 0 }"
                         v-for="precio in catalogos.precios_alquiler">
                        <div class="panel-body">

                            <input type="hidden" name="precio_alquiler[{{$index}}][id_precio]" class="form-control"
                                   style="width:100%;" v-model="precio.id">

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>4 horas </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][tarifa_4_horas]"
                                           v-model="precio.tarifa_4_horas" class="form-control" style="width:100%;"
                                           :config="config.inputmask.currency2"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>Hora </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][hora]" class="form-control"
                                           v-model="precio.hora" style="width:100%;"
                                           :config="config.inputmask.currency2"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>D&iacute;ario </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][diario]"
                                           v-model="precio.diario" class="form-control" style="width:100%;"
                                           :config="config.inputmask.currency2"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                    <!-- <input type="text" name="campo[tarifa_diario]" class="form-control" style="width:100%;" v-model="detalle.tarifa_diario" v-inputmask="detalle.tarifa_diario" :config="config.inputmask.currency2" :disabled="config.disableDetalle || !detalle.item_alquiler">-->
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>6 d&iacute;as </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][tarifa_6_dias]"
                                           v-model="precio.tarifa_6_dias" class="form-control" style="width:100%;"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>Semanal </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][semanal]"
                                           v-model="precio.semanal" class="form-control" style="width:100%;"
                                           :config="config.inputmask.currency2"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>15 d&iacute;as </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][tarifa_15_dias]"
                                           v-model="precio.tarifa_15_dias" class="form-control" style="width:100%;"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>28 d&iacute;as </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][tarifa_28_dias]"
                                           v-model="precio.tarifa_28_dias" class="form-control" style="width:100%;"
                                           :config="config.inputmask.currency2"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>30 d&iacute;as </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][tarifa_30_dias]"
                                           v-model="precio.tarifa_30_dias" class="form-control" style="width:100%;"
                                           :config="config.inputmask.currency2"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>Mensual </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="precio_alquiler[{{$index}}][mensual]"
                                           v-model="precio.mensual" class="form-control" style="width:100%;"
                                           :config="config.inputmask.currency2"
                                           :disabled="config.disableDetalle || !detalle.item_alquiler">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


            </div>


        </div>

    </div>


    <div class="ibox-title border-bottom" id="impuestos">
        <h5>Impuestos </h5>
    </div>

    <div class="ibox-content" style="border: 0px; display: block;">

        <div class="row">

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <label>Compra </label>
                <div class="input-group">
                    <select name="campo[uuid_compra]" v-model="detalle.uuid_compra" v-select2="detalle.uuid_compra"
                            :config="config.select2" :disabled="config.disableDetalle">
                        <option value="">Seleccione</option>
                        <option :value="impuesto.uuid_impuesto" v-for="impuesto in catalogos.impuestos">
                            {{impuesto.nombre}}
                        </option>
                    </select>
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <label>Venta </label>
                <div class="input-group">
                    <select name="campo[uuid_venta]" v-model="detalle.uuid_venta" v-select2="detalle.uuid_venta"
                            :config="config.select2" :disabled="config.disableDetalle">
                        <option value="">Seleccione</option>
                        <option :value="impuesto.uuid_impuesto" v-for="impuesto in catalogos.impuestos">
                            {{impuesto.nombre}}
                        </option>
                    </select>
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                </div>
            </div>

        </div>

    </div>

    <div class="ibox-title border-bottom" id="cuentas">
        <h5>Cuentas </h5>
    </div>

    <div class="ibox-content" style="border: 0px; display: block;">

        <div class="row">
            <ul class="nav nav-tabs" id="configuracionTabs">
                <li class="active"><a data-toggle="tab" href="#activos">Activos </a></li>
                <li><a data-toggle="tab" href="#ingresos">Ingresos </a></li>
                <li><a data-toggle="tab" href="#costos_gastos">Costos o gastos</a></li>
                <li><a data-toggle="tab" href="#variante">Variante</a></li>
            </ul>
        </div>

        <div class="tab-content row">

            <!-- Tab cuenta de activo-->
            <div class="ibox-content tab-pane active in" id="activos">
                <div class="row">
                    <div class="alert alert-dismissable alert-info">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <p><strong>Seleccione</strong> una sola cuenta de Activos</p>
                    </div>

                    <?php echo modules::run('inventarios/ocultoarbolactivo'); ?>
                </div>
            </div>

            <!-- Tab cuentas de ingresos-->
            <div class="ibox-content tab-pane fade" id="ingresos">
                <div class="row">
                    <?php echo modules::run('inventarios/ocultoarbolingreso'); ?>
                </div>
            </div>

            <!-- Tab cuentas de costos y gastos-->
            <div class="ibox-content tab-pane fade" id="costos_gastos">
                <div class="row">
                    <?php echo modules::run('inventarios/ocultoarbolcosto'); ?>
                </div>
            </div>

            <!-- Tab cuentas de variante-->
            <div class="ibox-content tab-pane fade" id="variante">
                <div class="row">
                    <?php echo modules::run('inventarios/ocultoarbolvariante'); ?>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <a href="<?php echo base_url('inventarios/listar') ?>"
                   class="btn btn-default form-control">Cancelar </a>
            </div>
            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <input type="submit" value="Guardar " class="btn btn-primary form-control"
                       :disabled="config.disableDetalle || config.disableGuardar">
                <input type="hidden" name="campo[id]" value="{{detalle.id}}">
            </div>
        </div>

    </div>


</div>


<!--panel del precios-->


<!--panel de cuentas asociadas al item-->


<!--panel de impuestos-->


<!--panel de atributos-->


<!--panel de tarifas de alquiler-->


<style>

    .onoffswitch-inner:before {
        padding-top: 1px !important;
        height: 20px !important;
    }

    .onoffswitch-inner::after {
        padding-top: 1px !important;
    }

    .input-group .select2-container--default .select2-selection--single {
        border-radius: 0px;
    }

    .navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:focus, .navbar-default .navbar-nav > .active > a:hover {
        background-color: white;
    }

    table.cuentas thead tr {
        background-color: #0679BF;
        color: #FFF;
        font-weight: bold;
        font-size: 16px;
    }

    table.cuentas tr td {
        border: 1px solid #DDD;
    }

    table.cuentas tbody tr td div.jstree {
        font-weight: bold;
        font-size: 14px;
    }

    table.cuentas tbody tr td i.jstree-icon {
        padding-right: 7px;
    }

    table.cuentas tbody tr td a.jstree-anchor {
        padding-right: 7px;
        color: #666666;
    }

    table.cuentas tbody tr td div.item-cuenta {
        width: 100%;
        height: 40px;
        width: 100%;
        background-color: #CCCCCC;
        padding: 10px;
        margin-bottom: 10px;
    }

    table.cuentas tbody tr td div.item-cuenta div.icono-cerrar {
        width: 10%;
        color: #FFF;
    }

    table.cuentas tbody tr td div.item-cuenta div.icono-cerrar a {
        color: #FFF;
        background-color: #990000;
    }

    table.cuentas tbody tr td div.item-cuenta div.cuenta-texto {
        width: 90%;
        color: #000;
        font-size: 14px;
        padding-right: 10px;
    }

</style>

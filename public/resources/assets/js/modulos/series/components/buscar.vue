<template>

    <div class="ibox border-bottom">
        <div class="ibox-title">
            <h5>Buscar serie</h5>
            <div class="ibox-tools">
                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
            </div>
        </div>
        <div class="ibox-content" style="display:none;">
            <!-- Inicia campos de Busqueda -->

            <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <label for="">No. de serie</label>
                    <input type="text" class="form-control" v-model="buscar.nombre">
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <label for="">Nombre de item</label>
                    <input type="text" class="form-control" v-model="buscar.nombre_item">
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <label for="categorias">Categor&iacute;a(s)</label>
                    <select class="form-control" multiple="true" v-select2="buscar.categorias" :config="config.select2">
                        <option value="">Seleccione</option>
                        <option :value="categoria.id" v-for="categoria in catalogos.categorias" v-html="categoria.nombre"></option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <label for="">Estado</label>
                    <select class="form-control" v-select2="buscar.estado" :config="config.select2">
                        <option value="">Seleccione</option>
                        <option :value="estado.etiqueta" v-for="estado in catalogos.estados" v-html="estado.valor"></option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" style="clear:both">
                    <label for="">Buscar en</label>
                    <select class="form-control" v-select2="buscar.buscar_en" :config="config.select2">
                        <option value="">Seleccione</option>
                        <option value="bodega">Bodega</option>
                        <option value="cliente">Cliente/Centro de facturaci&oacute;n</option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" v-if="buscar.buscar_en == 'bodega'">
                    <label for="">Bodega</label>
                    <select class="form-control" v-select2="buscar.bodega_id" :config="config.select2">
                        <option value="">Seleccione</option>
                        <option :value="bodega.bodega_id" v-for="bodega in catalogos.bodegas" v-html="bodega.nombre"></option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" v-if="buscar.buscar_en == 'cliente'">
                    <label for="">Cliente(s)</label>
                    <select class="form-control" v-select2="buscar.clientes" multiple="true" :config="config.select2">
                        <option value="">Seleccione</option>
                        <option :value="cliente.id" v-for="cliente in catalogos.clientes" v-html="cliente.nombre"></option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" v-if="buscar.buscar_en == 'cliente'">
                    <label for="">Centro(s) de facturaci&oacute;n</label>
                    <select class="form-control" v-select2="buscar.centros_facturacion" multiple="true" :config="config.select2">
                        <option value="">Seleccione</option>
                        <option :value="centro_facturacion.id" v-for="centro_facturacion in getCentrosFacturacion" v-html="centro_facturacion.nombre"></option>
                    </select>
                </div>


            </div>


            <div class="row">
                <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <input type="button" class="btn btn-default btn-block" value="Filtrar" @click="filtrar()"/>
                </div>
                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <input type="button" class="btn btn-default btn-block" value="Limpiar" @click="limpiar()"/>
                </div>
            </div>

            <!-- Termina campos de Busqueda -->
        </div>

    </div>

</template>

<script>

import listar from '../mixins/listar';

export default {

    mixins: [listar],

    props:{

        config: Object,
        buscar: Object,
        catalogos: Object,
        dom: Object

    },

    data:function(){

        return {};

    },

    methods: {

        filtrar: function () {

            var context = this;

            if(
                context.buscar.nombre !== '' ||
                context.buscar.nombre_item !== '' ||
                !_.isEmpty(context.buscar.categorias) ||
                context.buscar.estado !== '' ||
                context.buscar.buscar_en !== '' ||
                context.buscar.bodega_id !== '' ||
                !_.isEmpty(context.buscar.clientes) ||
                !_.isEmpty(context.buscar.centros_facturacion)
            )
            {
                var categorias = context.buscar.categorias ? JSON.parse(JSON.stringify(context.buscar.categorias)) : [];
                var clientes = context.buscar.clientes ? JSON.parse(JSON.stringify(context.buscar.clientes)) : [];
                var centros_facturacion = context.buscar.centros_facturacion ? JSON.parse(JSON.stringify(context.buscar.centros_facturacion)) : [];
                var aux = $.extend(JSON.parse(JSON.stringify(context.buscar)), {erptkn:window.tkn, categorias: categorias.join(), clientes: clientes.join(), centros_facturacion: centros_facturacion.join()});
                $('#'+ context.dom.grid_id).setGridParam({
                    url: phost() + 'series/ajax-listar',
                    datatype: "json",
                    postData: aux
                }).trigger('reloadGrid');
            }

        }

    },

    computed: {

      getCentrosFacturacion: function(){

              var context = this;
              var clientes_seleccionados = _.filter(context.catalogos.clientes, function(cliente){
                  return _.indexOf(context.buscar.clientes, _.toString(cliente.id)) > -1;
              });

              if(!_.isEmpty(clientes_seleccionados)){
                  var aux = [];
                  _.forEach(clientes_seleccionados, function(cliente_seleccionado){
                      _.forEach(cliente_seleccionado.centros_facturacion, function(centro_facturacion){
                          aux.push(centro_facturacion);
                      });
                  });
                  return aux;
              }

              return [];
          }

    },

    ready: function(){

        var context = this;

    }
};
</script>

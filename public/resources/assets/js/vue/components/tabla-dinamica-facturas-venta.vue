
<template>

    <table class="table table-noline tabla-dinamica">
        <thead>
            <tr>
                <th v-for="columna in columnas" width="{{columna.width}}%" colspan="{{columna.colspan}}">{{{columna.nombre}}}</th>
            </tr>
        </thead>
        <tbody :id="'itemventa' + $index" v-for="item in items" track-by="$index">
            <tr>
                <td style="width: 1%;">
                    <h3><a hrfe="#" @click="toggle($event)"><i class="fa fa-caret-right"></i></a></h3>
                </td>
                <td>
                    <select name="items[{{$index}}][categoria_id]" id="categoria_id{{$index}}" data-rule-required="true" v-model="item.categoria_id" class="chosen-select form-control" @change="limpiarFila($event, $index, item)" :disabled="item.id != ''">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in categorias">{{option.nombre}}</option>
                    </select>
                </td>
                <td>
                    <input  type="hidden"  id="comentario{{$index}}" name="items[{{$index}}][comentario]" value="{{item.comentario}}">
                    <div class="input-group">
                        <typeahead :item_url.sync="item_url" :categoria_id.sync="item.categoria_id" :parent_index="$index" :disabled="item.id != '' || item.categoria_id == '' && item.itemsList.length == 0"> </typeahead>
                        <input type="hidden" id="item_id{{$index}}" name="items[{{$index}}][item_id]" data-rule-required="true" :value="item.item_id" class="form-control" @change="popularUnidadAtributo($event, item, $index)">
                        <span class="input-group-btn">
                            <a id="boton{{$index}}" type="button" class="btn btn-default" rel=popover v-item-comentario="item.comentario"  :i="$index" :comentado="item.comentario"> <span class="fa fa-comment"></span></a>
                        </span>
                    </div>
                </td>
                <td>
                    <input type="text" name="items[{{$index}}][atributo_text]" class="form-control atributo" id="atributo_text{{$index}}" v-if="item.atributos.length == 0" v-model="item.atributo_text" :disabled="item.id != ''">
                    <select id="atributo_id{{$index}}" name="items[{{$index}}][atributo_id]" v-model="item.atributo_id" class="form-control" v-if="item.atributos.length > 0" :disabled="item.id != '' || item.atributos.length==0">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in item.atributos">{{option.nombre}}</option>
                    </select>
                </td>
                <td>
                    <input type="text" id="cantidad{{$index}}" name="items[{{$index}}][cantidad]" class="cantidad-item form-control" v-model="item.cantidad" data-rule-required="true" :disabled="item.id != ''" @keyup="calcularPrecioTotal($index)"  v-inputmask="item.cantidad" :config="{'mask':'9{1,8}[.9{0,4}]','greedy':false}"/>
                </td>
                <td>
                    <select id="unidad_id{{$index}}" name="items[{{$index}}][unidad_id]" data-rule-required="true" class="unidad-item form-control" v-model="item.unidad_id" :disabled="item.id !='' || item.item_id=='' && item.unidades.length==0" @change.prevent="calcularPrecioSegunUnidad($event, $index, item)">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in item.unidades">{{option.nombre}}</option>
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" id="precio_unidad{{$index}}" name="items[{{$index}}][precio_unidad]" v-model="item.precio_unidad" class="form-control precio_unidad" :disabled="item.precio_permiso =='0'" @keyup="calcularPrecioTotal($index)"/>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" id="precio_total{{$index}}" name="items[{{$index}}][precio_total]" v-model="item.precio_total" class="form-control precio_total" disabled="disabled"/>
                    </div>
                </td>
                <td v-show="$index>0 && factura.id=='' && item.id=='' || factura.id !='' && factura.uuid_venta!=''">
                    <button agrupador="items" class="btn btn-default btn-block eliminarItemBtn" type="button" @click="eliminarItemOrden($index, $event)">
                        <i class="fa fa-trash"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Eliminar</span>
                    </button>
                </td>
                <td v-show="$index==0">
                    <button class="btn btn-default btn-block agregarItemBtn" type="button" @click="agregarItemOrden($event)" :disabled="item.id != ''">
                        <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp; Agregar</span>
                    </button>
                </td>
            </tr>
            <tr id="itemsDatos{{$index}}">
                <td colspan="9" class="hide">
                    <table style="width: 100%;background-color: #A2C0DA">
                        <tbody>
                            <tr>
                                <td style="padding: 15px !important;" width="33%">
                                    <b>Impuesto</b>
                                    <!-- change="impuestoSeleccionado(item.impuesto,$index)" -->
                                    <select id="impuesto_id{{$index}}" name="items[{{$index}}][impuesto_id]" v-on:change="recomputeimpuesto($index)" v-model="item.impuesto_uuid" class="form-control item-impuesto" data-rule-required="true" :disabled="item.id != ''">
                                        <option value="">Seleccione</option>
                                        <option value="{{option.uuid_impuesto}}" v-for="option in item.impuestos">{{option.nombre}}</option>
                                    </select>
                                </td>
                                <td style="padding: 15px !important;" width="33%">
                                    <b>Descuento</b>
                                    <div class="input-group">
                                        <!-- ng-blur="descuentoCambio(item.descuento,$index)" -->
                                        <input type="text" id="descuento{{$index}}" v-model="item.descuento" name="items[{{$index}}][descuento]" class="form-control item-descuent" data-rule-required="true" data-inputmask="'mask':'9{1,3}[.*{1,2}]'" data-rule-range="[0,100]" :disabled="item.id != ''">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                                <td style="padding: 15px !important;" width="33%">
                                    <b>Cuenta</b>
                                    <select id="cuenta_id0" name="items[{{$index}}][cuenta_id]" data-rule-required="true" v-model="item.cuenta_uuid" class="form-control" :disabled="item.id != ''">
                                        <option value="">Seleccione</option>
                                        <option value="{{option.uuid}}" v-for="option in item.cuenta_transaccionales">{{option.codigo}} {{option.nombre}}</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        <input type="hidden" id="factura_item_id{{$index}}" name="items[{{$index}}][factura_item_id]" :disable="factura_id===''" v-model="item.id" />
        </tbody>
    </table>

</template>

<script>

var deleteItems = [];
//items_venta
export default {
    template: '#items_venta',
    props: {
        categorias: Array,
        impuestos: Array,
        cuenta_transaccionales: Array,
        factura: Object,
    },

    components:{
        'typeahead':require('./typeahead.vue')
    },

    ready: function () {

        var scope = this;

        //si existe variable infofactura
        if (typeof infofactura != 'undefined') {

            // popular items
            // vista de editar factura
            scope.$nextTick(function () {
                scope.$emit('popularTablaItems', infofactura.items);
            });
        }
    },
    data: function () {

        return {

            //typeahead
            item_url:'inventarios/ajax_get_typehead_items?ventas=1',

            columnas: [
                {nombre: 'Categor&iacute;a de item', width: '12', colspan: '2'},
                {nombre: 'Item a entregar', width: '12', colspan: '1'},
                {nombre: 'Atributo', width: '12', colspan: '1'},
                {nombre: 'Cantidad', width: '10', colspan: '1'},
                {nombre: 'Unidad', width: '10', colspan: '1'},
                {nombre: 'Precio unidad', width: '10', colspan: '1'},
                {nombre: 'Precio total', width: '8', colspan: '1'},
                {nombre: '', width: '1', colspan: '2'}
            ],
            items: [{
                    id: '',
                    categoria_id: '',
                    item_id: '',
                    atributo_id: '',
                    cantidad: 1,
                    unidad_id: '',
                    precio_unidad: '',
                    precio_total: '',
                    descuento: '0',
                    impuesto_uuid: '',
                    impuesto_porcentaje: '',
                    cuenta_uuid: '',
                    itemsList: [],
                    atributos: [],
                    unidades: [],
                    impuestos: this.impuestos,
                    cuenta_transaccionales: this.cuenta_transaccionales,
                    factura_item_id: '',
                    exonerado: null,
                    precio_permiso:editar_precio,
                    iteminfo: []
                }],
        };
    },
    events: {

        'update-precio_unidad':function() {

            var context = this;
            _.forEach(context.items, function(articulo, key){
                var precio_unidad = _.find(articulo.precios, function(precio){
                    return precio.id == context.factura.lista_precio_id;
                });
                articulo.precio_unidad = !_.isEmpty(precio_unidad) ? precio_unidad.pivot.precio : 0;
                context.$parent.calcularPrecioTotal(key);
            });
        },

        //se ejecuta cuando se selecciona un item de la lista desplegable
        'update-item':function(item) {

            var context = this;
            context.items[item.parent_index].itemsList=[item];

            var selected_categoria = _.head(item.categoria);
            context.items[item.parent_index].categoria_id = selected_categoria.id;
            context.items[item.parent_index].item_id = item.id;
            context.items[item.parent_index].atributos = item.atributos;
            context.items[item.parent_index].atributo_id = item.atributo_id;
            context.items[item.parent_index].cantidad = '';
            context.items[item.parent_index].cuenta_id = item.cuenta_id;
            context.items[item.parent_index].cuentas = item.cuentas;//string con json para el filtro de cuentas
            context.items[item.parent_index].cuenta_transaccionales = context.filtrarCuentas(item).length > 0 ? context.filtrarCuentas(item) : context.cuenta_transaccionales,
            context.items[item.parent_index].unidades = item.unidades;
            context.items[item.parent_index].tipo_id = item.tipo_id;
            Vue.nextTick(function(){
                var precio_unidad = _.find(item.precios, function(precio){
                    return precio.id == context.factura.lista_precio_id;
                });
                context.items[item.parent_index].unidad_id = item.unidad_id;
                context.items[item.parent_index].precios = item.precios;
                context.items[item.parent_index].precio_unidad = !_.isEmpty(precio_unidad) ? precio_unidad.pivot.precio : 0;
                context.items[item.parent_index].impuesto_id = item.impuesto_id;
                context.items[item.parent_index].impuesto_uuid = item.impuesto_uuid;
            });
        },

        //
        // Popular tabla de items
        // al seleccionar, empezar factura desde
        // (orden venta, contrato venta, etc)
        //
        popularTablaItems: function (items) {
            var scope = this;

            if (typeof items === 'undefined' || items.length === 0) {
                this.resetItems();
                return false;
            }

            scope.items = [];
            $.each(items, function (index, item) {

                //Lista de Items
                var categoria = _.find(scope.categorias, function (categoria) {
                    return categoria.id == item.categoria_id;
                });

                var datos = $.extend({erptkn: tkn},categoria,{'ventas': 1, item_id:item.item_id});
                scope.$http.post({
                    url: window.phost() + "inventarios/ajax-get-items-categoria",
                    method:'POST',
                    data:datos
                }).then(function(response){

                    if(!_.isEmpty(response.data)){

                        //context.$broadcast('fill-typeahead',response.data.items);

                        var itemsList = !_.isEmpty(categoria) ? JSON.parse(JSON.stringify(response.data.items)) : [];

                        //Item Info
                        var iteminfo = _.find(itemsList, function (iteminfo) {
                            return iteminfo.id == item.item_id;
                        });

                        //Lista atributos
                        var atributosList = !_.isEmpty(iteminfo) ? iteminfo.atributos : [];

                        //Lista unidades
                        var unidadesList = !_.isEmpty(iteminfo) && iteminfo.unidades.length > 0 ? iteminfo.unidades : [];

                        scope.items.push({
                            id: item.id,
                            categoria_id: item.categoria_id,
                            item_id: item.item_id,
                            atributo_id: item.atributo_id,
                            atributo_text: item.atributo_text,
                            cantidad: item.cantidad,
                            unidad_id: item.unidad_id,
                            precio_unidad: item.precio_unidad,
                            precio_total: item.precio_total,
                            descuento: item.descuento,
                            impuesto_uuid: typeof item.impuesto != 'undefined' && item.impuesto != null ? item.impuesto.uuid_impuesto : '',
                            impuesto_porcentaje: typeof item.impuesto != 'undefined' && item.impuesto != null ? item.impuesto.impuesto : '',
                            cuenta_uuid: typeof item.cuenta != 'undefined' && item.cuenta != null ? item.cuenta.uuid_cuenta : '',
                            factura_item_id: '',
                            itemsList: itemsList,
                            atributos: atributosList,
                            unidades: unidadesList,
                            impuestos: scope.impuestos,
                            cuenta_transaccionales: scope.filtrarCuentas(iteminfo),
                            comentario: item.comentario
                        });

                        Vue.nextTick(function(){
                            //Asigno el nombre al typeahead luego ver como asigno el nombre
                            scope.$children[scope.items.length - 1].$emit('set-typeahead-nombre',iteminfo.nombre);
                            _.forEach(scope.items, function (value, key) {
                                scope.$parent.calcularPrecioTotal(key);
                            });
                        });

                    }

                });

            });

        }
    },
    methods: {

        filtrarCuentas: function(item){

            var context = this;
            if(item.cuentas.length > 2){
                return _.filter(context.cuenta_transaccionales, function(cuenta){

                    return item.cuentas.indexOf("ingreso:"+ cuenta.id +"\"") > -1
                });
            }
            return context.cuenta_transaccionales;

        },
        recomputeimpuesto: function(index) {
            var impuestonumber = 0;
            $.each(JSON.parse(impuestos), function (e, value) {
                if (event.target.value == value.uuid_impuesto) { 
                    impuestonumber = value.impuesto;
                    
                }
            });
            this.$parent.impuesto = ( impuestonumber * this.$parent.subtotal ) / 100
            
        },
        calcularPrecioTotal: function (index) {
            this.$parent.calcularPrecioTotal(index);
        },
        toggle: function (e) {
            this.$root.toggleSubTabla(e);
        },
        // Popular campo Items
        // segun categoria seleccionada
        // se ejecuta al cambiar la categoria
        limpiarFila: function (e, index, item, items) {
            e.preventDefault();
            item.item_id = "";
            item.unidad_id = "";
            item.unidades = [];
            item.precio_unidad = 0;
            item.cantidad = 1;
            //no se usa en el refactory typeahead
            //this.$parent.popularItems(e, index, item, items);
        },
        //
        // Popular Campo de Unidad y Atributo
        // segun Item Seleccionado
        //
        popularUnidadAtributo: function (e, item, index) {

            e.preventDefault();

            this.$parent.popularUnidadAtributo(e, item, index);
        },
        calcularPrecioSegunUnidad: function (e, item, index) {
            e.preventDefault();
            this.$parent.calcularPrecioSegunUnidad(e, item, index);
        },
        //
        // A gregar un nuevo Item a la tabla
        //
        agregarItemOrden: function (e) {
            if (typeof e == 'undefined') {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
            }

            this.items.push({
                id: '',
                categoria_id: '',
                item_id: '',
                atributo_id: '',
                cantidad: '1',
                unidad_id: '',
                precio_unidad: '',
                precio_total: '',
                descuento: '0',
                impuesto_uuid: '',
                impuesto_porcentaje: '',
                cuenta_uuid: '',
                itemsList: [],
                atributos: [],
                unidades: [],
                impuestos: this.impuestos,
                cuenta_transaccionales: this.cuenta_transaccionales,
                factura_item_id: '',
                exonerado: null,
                precio_permiso:editar_precio,
                iteminfo: []
            });
        },
        resetItems: function () {

            //reset tabla items
            this.items = [{
                    id: '',
                    categoria_id: '',
                    item_id: '',
                    atributo_id: '',
                    cantidad: '1',
                    unidad_id: '',
                    precio_unidad: '',
                    precio_total: '',
                    descuento: '0',
                    impuesto_uuid: '',
                    impuesto_porcentaje: '',
                    cuenta_uuid: '',
                    itemsList: [],
                    atributos: [],
                    unidades: [],
                    impuestos: this.impuestos,
                    cuenta_transaccionales: this.cuenta_transaccionales,
                    factura_item_id: '',
                    exonerado: null,
                    precio_permiso:editar_precio,
                    iteminfo: []
                }];
        },
        eliminarItemOrden: function (index, e) {
            e.preventDefault();

            var modal = $('#opcionesModal');
            var id = this.items[index]['id'];

            if (typeof id != 'undefined' && id != '') {

                var botones = [
                    '<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>',
                    '<button class="btn btn-w-m btn-danger" type="button" id="eliminarServicioBtn">Aceptar</button>'
                ].join('\n');

                //Modal
                this.$root.modal.titulo = 'Confirme';
                this.$root.modal.contenido = '<h3>&#191;Esta seguro que desea eliminar?</h3><p>El item sera eliminado al hacer clic en Guardar!</p>';
                this.$root.modal.footer = botones;

                modal.modal('show');
                modal.on('click', '#eliminarServicioBtn', {id: id, index: index}, this.eliminarItem);

            } else {
                //eliminar fila
                this.items.splice(index, 1);
            }
        },
        // Al hacer clic en el boton del modal
        // Elminar item de la tabla
        // y poner en array delete_items
        eliminarItem: function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = typeof e.data.id != 'undefined' ? e.data.id : '';
            var index = typeof e.data.index != 'undefined' ? e.data.index : '';
            var modal = $('#opcionesModal');
            modal.modal('hide');

            //verificar si existe id
            if (id == '') {
                return false;
            }

            //verificar si item ya existe en items a eliminar
            var existe = _.find(deleteItems, function (item) {
                return item == id;
            });

            if (typeof existe != 'undefined') {
                return;
            }

            //agregar a array de items a eliminar
            deleteItems.push(id);
            this.$parent.delete_items = deleteItems;

            //eliminar fila
            this.items.splice(index, 1);

            // verificar si ya no existen items en la tabla
            // insertar un item con campos en blancos
            if (this.items.length == 0) {
                this.resetItems();
            }
        }
    },
    watch:{
        'items':{
            handler:function(val ,old){
                var tipos = _.map(this.items,'tipo_id');

                if(_.includes(tipos,4) || _.includes(tipos,5)){
                    $("#bodega_id").prop('disabled',false);
                    return;
                }
                $("#bodega_id").prop('disabled',true);
                return;

            },
            deep:true
        }
    }
};

</script>

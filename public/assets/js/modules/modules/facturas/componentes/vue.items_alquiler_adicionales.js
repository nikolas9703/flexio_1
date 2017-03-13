var deleteItems = [];
Vue.component('items_alquiler_adicionales', {
    template: '#items_alquiler_adicionales',
    props: {
        categorias: Array,
        impuestos: Array,
        cuenta_transaccionales: Array,
        cargos_adicionales_checked: String,
        factura: Object,
    },
    data: function () {

        return {
            columnas: [
                {nombre: 'Categor&iacute;a de item', width: '12', colspan: '2'},
                {nombre: 'Item', width: '12', colspan: '1'},
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
                    iteminfo: []
                }],
        };
    },
    events: {
        //
        // Popular tabla de items
        // al seleccionar, empezar factura desde
        // (orden venta, contrato venta, etc)
        //
        popularTablaItemsAdicionales: function (items) {
            var scope = this;

            if (typeof items === 'undefined' || items.length === 0) {
                this.resetItems();
                return false;
            }

            var data = [];
            $.each(items, function (index, item) {

                //Lista de Items
                var categoria = _.find(scope.categorias, function (categoria) {
                    return categoria.id == item.categoria_id;
                });
                var itemsList = !_.isEmpty(categoria) ? categoria.items : [];

                //Item Info
                var iteminfo = _.find(itemsList, function (iteminfo) {
                    return iteminfo.id == item.item_id;
                });

                //Lista atributos
                var atributosList = !_.isEmpty(iteminfo) ? iteminfo.atributos : [];

                //Lista unidades
                var unidadesList = !_.isEmpty(iteminfo) && iteminfo.unidades.length > 0 ? iteminfo.unidades : [];

                data.push({
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
                    cuenta_transaccionales: scope.cuenta_transaccionales,
                    comentario: item.comentario
                });
            });

            scope.items = data;

            // Trigger:
            // Calcular precio total
            // de cada item
            //
            _.forEach(data, function (value, key) {
                scope.$parent.calcularPrecioTotal(key);
            });
        }
    },
    methods: {
        calcularPrecioTotal: function (index) {
            this.$parent.calcularPrecioTotal(index);
        },
        toggle: function (e) {
            this.$root.toggleSubTabla(e);
        },
        // Popular campo Items
        // segun categoria seleccionada
        popularItems: function (e, index, item, items) {
            e.preventDefault();

            this.$parent.popularItems(e, index, item, items);
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
                cantidad: 1,
                periodo_tarifario_id: '',
                precio_unidad: '',
                precio_total: '',
                descuento: '0',
                impuesto_uuid: '',
                impuesto_porcentaje: '',
                cuenta_uuid: '',
                periodo_tarifario: [],
                itemsList: [],
                atributos: [],
                unidades: [],
                impuestos: this.impuestos,
                cuenta_transaccionales: this.cuenta_transaccionales,
                factura_item_id: '',
                exonerado: null,
            });
        },
        resetItems: function () {

            this.items = [{
                    id: '',
                    categoria_id: '',
                    item_id: '',
                    atributo_id: '',
                    cantidad: '',
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
                }];
        },
        eliminarItemOrden: function (index, e) {
            e.preventDefault();

            var modal = $('#opcionesModal');
            var id = this.items[index]['id'];

            if (typeof id != 'undefined' && id != '') {

                var botones = [
                    '<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>',
                    '<button class="btn btn-w-m btn-danger" type="button" id="elimiarServicioBtn">Eliminar</button>'
                ].join('\n');

                //Modal
                this.$root.modal.titulo = 'Confirme';
                this.$root.modal.contenido = '&#191;Esta seguro que desea eliminar?';
                this.$root.modal.footer = botones;

                modal.modal('show');
                modal.on('click', '#elimiarServicioBtn', {id: id, index: index}, this.eliminarItem);

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
            this.$parent.$parent.delete_items = deleteItems;

            //eliminar fila
            this.items.splice(index, 1);

            // verificar si ya no existen items en la tabla
            // insertar un item con campos en blancos
            if (this.items.length == 0) {
                this.resetItems();
            }
        }
    },
});

Vue.component('items_alquiler', {
    template: '#items_alquiler',
    props: {
        categorias: Array,
        impuestos: Array,
        cuenta_transaccionales: Array,
        factura: Object,
    },
    data: function () {

        return {
            columnas: [
                {nombre: 'Item', width: '12', colspan: '1'},
                {nombre: 'Cantidad', width: '10', colspan: '1'},
                {nombre: 'Rango de fechas', width:'10', colspan: '1'},
                {nombre: 'Tarifa pactada', width: '10', colspan: '1'},
                {nombre: 'Periodo tarifario', width: '10', colspan: '1'},
                {nombre: 'Monto del periodo', width: '10', colspan: '1'},
                {nombre: 'Cantidad de periodos', width: '10', colspan: '1'},
                {nombre: 'Total', width: '8', colspan: '1'}
            ],
            items: [{
                    id: '',
                    nombre: '',
                    categoria_id: '',
                    cantidad: '',
                    rango_fecha: '',
                    tarifa_pactada: '',
                    periodo_tarifario: '',
                    monto_periodo: '',
                    cantidad_periodo: '',
                    impuesto_porcentaje: '',
                    descuento:'',
                    contratoId: '',
                    cuenta_id:'',
                    precio_total: ''
                }]
        };
    },
    events: {
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
                    id: item.item.id,
                    nombre: item.item.nombre,
                    categoria_id: item.categoria_id,
                    atributo_text: item.atributo_text,
                    cantidad: item.cantidad,
                    rango_fecha: item.rango_fecha,
                    tarifa_pactada: item.tarifa_pactada,
                    periodo_tarifario_id: item.periodo.id,
                    periodo_tarifario: item.periodo.nombre,
                    monto_periodo: item.tarifa_monto,
                    cantidad_periodo: item.tarifa_cantidad_periodo,
                    impuesto_id: item.impuesto_id,
                    impuesto_porcentaje: item.impuesto,
                    descuento: item.descuento_total,
                    cuenta_id: item.cuenta_id,
                    precio_total: item.tarifa_monto * item.tarifa_cantidad_periodo
                });
            });

            scope.items = data;

            // Trigger:
            // Calcular precio total
            // de cada item
            //
        }
    },
    methods: {

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
                cantidad: '',
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


var entrega_item = Vue.component('entrega_item',{

    template:'#entrega_item',

    props: ['parent_index','parent_articulo','parent_articulos'],

    ready: function ()
    {

        var context = this;
        context.myReady();

    },

    events:{

        refrescarEntregaItem:function(){

            var context = this;
            context.myReady();

        }

    },

    data: function()
    {

        return {
            vista:vista,

            disabledEditar:false,
            disabledEditarTabla:false,
            disabledAddRow:false,
            fecha_estimada_devolucion : '',
            categorias: categorias,//catalogo from controller
            ciclos_tarifarios: ciclos_tarifarios,//catalogo from controller,
            bodegas: JSON.parse(bodegas),
            ItemsNoDisponibles: typeof items_disponibles != 'undefined' ? items_disponibles : '',
            articulos:[],
            entrega_alquiler: (vista == 'editar') ? JSON.parse(entrega_alquiler) : '',

        };

    },

    computed: {

        getSeries:function(){

            //recordar colocar condicion
            //para validar que sea el mismo
            //item y la misma categoria
            //también borrar la condicion que evita que se rompa el codigo de
            //jose luis
            var context = this;
            var series_seleccionadas = [];

            _.forEach(context.parent_articulos, function(fila){
                _.forEach(fila.detalles, function(subfila){
                    series_seleccionadas.push(subfila.serie);
                });
            });

            return _.filter(context.parent_articulo.series, function(serie){
                console.log(series_seleccionadas, serie.codigo, series_seleccionadas.indexOf(serie.codigo));
                return series_seleccionadas.indexOf(serie.codigo) === -1;
            });

        }

    },

    methods:
    {

        myReady:function()
        {
            var context = this;
            var item = _.find(context.parent_articulo.items, function(item){
                return item.id == context.parent_articulo.item_id;
            });
            var cantidad_restante = context.parent_articulo.cantidad_restante;
            var serializable = typeof item !="undefined" && (item.tipo_id == '5' || item.tipo_id == '8');

            context.articulos = [];
            var fecha_fin = '';

            if(context.vista == 'editar')
            {

                if(context.entrega_alquiler.estado_id > '2')//anulado o terminado
                {
                    context.disabledEditar = true;
                }
                else if(context.entrega_alquiler.estado_id == '2')//vigente
                {
                    context.disabledEditarTabla = true;
                }

                _.forEach(context.parent_articulo.detalles, function(detalle){

                    if(context.entrega_alquiler.id == detalle.operacion_id)//falta condicion del filtro
                    {

                        context.articulos.push({
                            serializable: serializable,
                            serie: detalle.serie,
                            cantidad: detalle.cantidad,
                            bodega_id: detalle.bodega_id,
                            fecha_devolucion_estimada:detalle.fecha_format,
                            ubicacion_id: detalle.bodega_id,
                            disabledAddRow:true
                        });

                    }

                });
            }
            else if(serializable)
            {
                var fecha_devolucion_estimada = '';
                if(vista=='crear') {
                    //if(typeof articulo.fecha_devolucion_estimada != 'undefined') {

                    //}
                }
                var i = 0;
                if (this.$root.entrega_alquiler.fecha_fin_contrato!=''){
                    fecha_fin = this.$root.entrega_alquiler.fecha_fin_contrato;
                }

                for(var i=0;i<cantidad_restante;i++)
                {
                    context.articulos.push({
                        serializable: serializable,
                        serie:'',
                        cantidad: 1,
                        bodega_id:'',
                        fecha_devolucion_estimada:fecha_fin,
                        ubicacion_id:'',
                        disabledAddRow:true
                    });
                }

            }
            else
            {
                if (this.$root.entrega_alquiler.fecha_fin_contrato!=''){
                    fecha_fin = this.$root.entrega_alquiler.fecha_fin_contrato;
                }

                context.articulos.push({
                    serializable: false,
                    serie:'',
                    cantidad: cantidad_restante,
                    bodega_id:'',
                    fecha_devolucion_estimada:fecha_fin,
                    ubicacion_id:'',
                    disabledAddRow:true
                });

            }

            if(context.vista == 'crear')
            {
                //se usa para unir los objetos padre-hijo
                context.parent_articulos[context.parent_index].detalles = context.articulos;
            }
            context.activarDatepicker();

        },

        activarDatepicker:function(){

            var context = this;
            Vue.nextTick(function(){

                $("#entrega_item_detalles"+ context.parent_index).find('.fecha').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    numberOfMonths: 1
                });

            });


        },

        getControles:function(){

            var context = this;
            var cantidad_restante = parseInt(context.parent_articulo.cantidad_restante);
            var cantidad_entrega = _.sumBy(context.articulos, function(articulo){
                return parseInt(articulo.cantidad) || 0;
            });

            return {
                sobrepasa:!(cantidad_restante > cantidad_entrega),
                cantidad_restante: cantidad_restante,
                cantidad_entrega:cantidad_entrega
            };

        },

        verificaCantidad: function(){

            var context = this;
            var controles = context.getControles();

            _.forEach(context.articulos, function(articulo){
                articulo.cantidad = parseInt(articulo.cantidad) || 0;
                articulo.disabledAddRow = controles.sobrepasa;
            });

            if(controles.sobrepasa)//sobrepaso la cantidad restante o el valor es igual a la cantidad restante
            {
                for(var i=context.articulos.length;i>0;i--)
                {

                    var controles = context.getControles();
                    var aux = parseInt(controles.cantidad_restante - (controles.cantidad_entrega - context.articulos[i-1].cantidad)) || 0;
                    context.articulos[i-1].cantidad = aux < 0 ? 0 : aux;

                }
            }

        },

        cambiarCantidad:function()
        {

            var context = this;
            context.verificaCantidad();

        },

        addRow:function(e)
        {
            var context = this;
            var item = _.find(context.parent_articulo.items, function(item){
                return item.id == context.parent_articulo.item_id;
            });
            var serializable = (item.tipo_id == '5' || item.tipo_id == '8');
            e.preventDefault();

            context.articulos.push({
                serializable: serializable,
                serie:'',
                cantidad: serializable ? 1 : '',
                bodega_id:'',
                fecha_devolucion_estimada:'',
                disabledAddRow:true
            });

            context.verificaCantidad();
            context.activarDatepicker();
        },

        removeRow:function(index, e)
        {
            e.preventDefault();
            this.articulos.splice(index,1);
            this.verificaCantidad();
        }

    }

});


var entrega_items = Vue.component('entrega_items',{

    template:'#entrega_items',

    ready: function ()
    {
        var context = this;

        if(context.vista == 'editar')
        {
            var entrega_alquiler_json = JSON.parse(entrega_alquiler);
            var articulos = entrega_alquiler_items;

            context.setArticulos(articulos);

            if(entrega_alquiler_json.estado_id > '2')//anulado o terminado
            {
                context.disabledEditar = true;
            }
            else if(entrega_alquiler_json.estado_id == '2')//vigente
            {
                context.disabledEditarTabla = true;
            }
        }
    },

    events: {

        'cambiarEmpezable': function(articulos){

            var context = this;//articulos = contratos_items from formulario.js
            context.setArticulos(articulos);

        }

    },

    data: function()
    {
        return {
            vista:vista,

            disabledEditar:false,
            disabledEditarTabla:true,

            //catalogos
            empezables: empezables,
            categorias: categorias,//catalogo from controller
            ciclos_tarifarios: ciclos_tarifarios,//catalogo from controller,

            articulos:[]

        };

    },

    methods:
    {

        cambiarCaret: function(articulo)
        {

            articulo.caret = articulo.caret == 'fa-caret-down' ? 'fa-caret-right' : 'fa-caret-down';

        },

        setArticulos:function(articulos)//contrato_item => crear
        {
            var context = this;

            context.articulos = [];
            _.forEach(articulos, function(articulo){
                var categoria = _.find(context.categorias, function(categoria){
                    return categoria.id==articulo.categoria_id;
                });

                var cantidad_restante = articulo.cantidad - (articulo.entregado + articulo.por_entregar);
                if(cantidad_restante > 0 || context.vista == 'editar')
                {

                    context.articulos.push(
                        {
                            categoria_id:articulo.categoria_id,
                            items:categoria.items_contratos_alquiler,
                            series:articulo.item.seriales,
                            atributos:articulo.item.atributos,
                            atributo_id:articulo.atributo_id,
                            atributo_text:articulo.atributo_text,
                            detalles:(context.vista == 'editar') ? articulo.contratos_items_detalles : [],
                            caret:'fa-caret-right',
                            item_id:articulo.item_id,
                            ciclo_id:articulo.ciclo_id,
                            tarifa:articulo.tarifa,
                            cantidad_restante:cantidad_restante
                        }
                    );

                }

            });

            Vue.nextTick(function(){

                context.$broadcast('refrescarEntregaItem');

            });

        },

        cambiarCategoria:function (articulo, index)
        {
            var categoria = _.find(this.categorias, function(categoria){
                return categoria.id==articulo.categoria_id;
            });

            if(_.isEmpty(categoria))
            {
                articulo.items = [];
            }
            else
            {
                articulo.items = categoria.items_contratos_alquiler;
            }
        },

        addRow:function(e)
        {
            var context = this;
            e.preventDefault();
            context.articulos.push({categoria_id:'',items:[],caret:'fa-caret-right'});

            Vue.nextTick(function(){
                $('#entregasAlquilerItems').find('#entrega_item'+ (context.articulos.length - 1)).find('input').inputmask();
            });
        },

        removeRow:function(index, e)
        {
            e.preventDefault();
            this.articulos.splice(index,1);
        }

    }

});
